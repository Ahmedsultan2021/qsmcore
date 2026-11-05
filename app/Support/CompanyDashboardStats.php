<?php

namespace App\Support;

use App\Models\Audit;
use App\Models\Capa;
use App\Models\Department;
use App\Models\Report;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Operational figures shared by the company dashboard and the risk register.
 * Assessed risk scores are never derived from these counts.
 */
class CompanyDashboardStats
{
    /** @var Collection<int, Department>|null */
    private ?Collection $departments = null;

    public function __construct(
        private readonly int $companyId,
        private readonly ?int $departmentId,
        private readonly ?string $themeSlug,
    ) {
    }

    public static function make(int $companyId, ?int $departmentId, ?string $themeSlug): self
    {
        $theme = in_array($themeSlug, ['safety', 'quality'], true) ? $themeSlug : null;

        return new self($companyId, $departmentId, $theme);
    }

    /**
     * @return array<string, mixed>
     */
    public function dashboardPayload(): array
    {
        $departments = $this->departments();
        $filteredIds = $this->filteredDepartmentIds();
        $departmentId = $this->departmentId;

        $openSafety = $this->openReportCount($filteredIds, 'safety');
        $openQuality = $this->openReportCount($filteredIds, 'quality');
        $closedReports = $this->closedReportCount($filteredIds, $this->themeSlug);

        $overdueCapaQuery = $this->overdueCapaQuery($departmentId);
        $overdueCAPAs = (clone $overdueCapaQuery)->count();
        $earliestOverdueDue = (clone $overdueCapaQuery)->orderBy('due_date')->value('due_date');

        $nextCapa = Capa::where('company_id', $this->companyId)
            ->where('status', '!=', 'closed')
            ->whereNotNull('due_date')
            ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
            ->orderBy('due_date')
            ->first(['id', 'title', 'due_date', 'status']);

        $nextAudit = Audit::where('company_id', $this->companyId)
            ->whereDate('audit_date', '>=', now()->toDateString())
            ->when($departmentId, fn ($q) => $q->whereHas(
                'reports',
                fn ($reports) => $reports->where('department_id', $departmentId)
            ))
            ->orderBy('audit_date')
            ->first(['id', 'name', 'audit_date']);

        $statusCounts = $this->reportQuery($filteredIds, $this->themeSlug)
            ->selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->map(fn ($v) => (int) $v);

        $recentReports = $this->reportQuery($filteredIds, $this->themeSlug)
            ->with(['department.formTemplates.themes', 'forms.formTemplate.themes'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'title' => $r->title,
                'dept' => $r->department?->name,
                'type' => $r->themeLabel(),
                'status' => $r->status,
                'date' => $r->report_date?->format('d-M-Y'),
                'url' => route('companies.departments.reports.show', [$r->department_id, $r->id]),
            ]);

        $openSafetyAlerts = Report::whereIn('department_id', $filteredIds)
            ->whereNotIn('status', ['approved', 'rejected'])
            ->forTheme('safety')
            ->with('department')
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'title' => $r->title,
                'status' => $r->status,
                'url' => route('companies.departments.reports.show', [$r->department_id, $r->id]),
            ]);

        $totalVol = $this->reportQuery($filteredIds, $this->themeSlug)->count();
        $rawVolume = $this->reportQuery($filteredIds, $this->themeSlug)
            ->selectRaw('department_id, COUNT(*) as cnt')
            ->groupBy('department_id')
            ->orderByDesc('cnt')
            ->limit(5)
            ->get();

        $volumeByDept = $rawVolume->map(fn ($r) => [
            'name' => $departments->firstWhere('id', $r->department_id)?->name ?? 'Unknown',
            'count' => (int) $r->cnt,
            'percent' => $totalVol > 0 ? (int) round($r->cnt / $totalVol * 100) : 0,
        ]);

        return [
            'departments' => $departments,
            'stats' => [
                'openSafety' => $openSafety,
                'openQuality' => $openQuality,
                'closedReports' => $closedReports,
                'overdueCAPAs' => $overdueCAPAs,
            ],
            'statusCounts' => $statusCounts,
            'recentReports' => $recentReports,
            'openSafetyAlerts' => $openSafetyAlerts,
            'volumeByDept' => $volumeByDept,
            'attention' => [
                'overdueDue' => $earliestOverdueDue
                    ? Carbon::parse($earliestOverdueDue)->format('d M Y')
                    : null,
                'nextCapa' => $nextCapa ? [
                    'title' => $nextCapa->title,
                    'due_date' => $nextCapa->due_date?->format('d M Y'),
                    'overdue' => $nextCapa->due_date && $nextCapa->due_date->toDateString() < now()->toDateString(),
                    'url' => route('companies.capa.show', $nextCapa->id),
                ] : null,
                'nextAudit' => $nextAudit ? [
                    'name' => $nextAudit->name,
                    'date' => $nextAudit->audit_date?->format('d M Y'),
                    'url' => route('companies.audits.show', $nextAudit->id),
                ] : null,
            ],
        ];
    }

    /**
     * Figures and per-department signals for the risk register.
     * Closed reports here ignore the dashboard theme filter.
     *
     * @return array{
     *     stats: array{openSafety: int, openQuality: int, closedReports: int, overdueCAPAs: int, overdueDue: ?string},
     *     pressuredDepartments: int,
     *     departments: list<array{id: int, name: string, heat: bool, pressure: bool}>,
     *     signals: array<int, array{open_safety: int, open_quality: int, overdue_capas: int}>
     * }
     */
    public function registerContext(): array
    {
        $departments = $this->departments();
        $allIds = $departments->pluck('id');
        $filteredIds = $this->filteredDepartmentIds();

        $openSafety = $this->countsByDepartment($allIds, 'safety', 'open');
        $openQuality = $this->countsByDepartment($allIds, 'quality', 'open');
        $overdueCapas = $this->overdueCapasByDepartment($allIds);
        $heatIds = array_flip($this->heatDepartmentIds($departments));

        $signals = [];
        $departmentRows = [];
        $pressuredDepartments = 0;
        $filteredLookup = array_flip($filteredIds->map(fn ($id) => (int) $id)->all());

        foreach ($departments as $department) {
            $id = (int) $department->id;
            $signal = [
                'open_safety' => $openSafety[$id] ?? 0,
                'open_quality' => $openQuality[$id] ?? 0,
                'overdue_capas' => $overdueCapas[$id] ?? 0,
            ];
            $pressure = $signal['open_safety'] > 0
                || $signal['open_quality'] > 0
                || $signal['overdue_capas'] > 0;

            $signals[$id] = $signal;
            $departmentRows[] = [
                'id' => $id,
                'name' => $department->name,
                'heat' => isset($heatIds[$id]),
                'pressure' => $pressure,
            ];

            if ($pressure && isset($filteredLookup[$id])) {
                $pressuredDepartments++;
            }
        }

        $overdueQuery = $this->overdueCapaQuery($this->departmentId);
        $earliestOverdueDue = (clone $overdueQuery)->orderBy('due_date')->value('due_date');

        return [
            'stats' => [
                'openSafety' => $this->openReportCount($filteredIds, 'safety'),
                'openQuality' => $this->openReportCount($filteredIds, 'quality'),
                'closedReports' => $this->closedReportCount($filteredIds, null),
                'overdueCAPAs' => (clone $overdueQuery)->count(),
                'overdueDue' => $earliestOverdueDue
                    ? Carbon::parse($earliestOverdueDue)->format('d M Y')
                    : null,
            ],
            'pressuredDepartments' => $pressuredDepartments,
            'departments' => $departmentRows,
            'signals' => $signals,
        ];
    }

    /**
     * One review badge. Likelihood and impact stay as the assessor entered them.
     *
     * @param  array{open_safety?: int, open_quality?: int, overdue_capas?: int}  $department
     * @return array{key: string, label: string}|null
     */
    public static function signalFor(string $status, string $category, array $department): ?array
    {
        if (in_array($status, ['closed', 'mitigated'], true)) {
            return null;
        }

        if (
            in_array($status, ['in_progress', 'monitoring'], true)
            && (int) ($department['overdue_capas'] ?? 0) > 0
        ) {
            return ['key' => 'treatment_overdue', 'label' => 'Treatment overdue'];
        }

        if ($category === 'operational' && (int) ($department['open_safety'] ?? 0) > 0) {
            return ['key' => 'active_exposure', 'label' => 'Active exposure'];
        }

        if ($category === 'compliance' && (int) ($department['open_quality'] ?? 0) > 0) {
            return ['key' => 'quality_exposure', 'label' => 'Open quality exposure'];
        }

        return null;
    }

    /**
     * @return Collection<int, Department>
     */
    private function departments(): Collection
    {
        return $this->departments ??= Department::query()
            ->where('company_id', $this->companyId)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * @return Collection<int, int|string>
     */
    private function filteredDepartmentIds(): Collection
    {
        $ids = $this->departments()->pluck('id');

        if ($this->departmentId === null) {
            return $ids;
        }

        return $ids->intersect([$this->departmentId]);
    }

    /**
     * @param  Collection<int, int|string>  $departmentIds
     */
    private function reportQuery(Collection $departmentIds, ?string $theme)
    {
        return Report::query()
            ->whereIn('department_id', $departmentIds)
            ->forTheme($theme);
    }

    /**
     * @param  Collection<int, int|string>  $departmentIds
     */
    private function openReportCount(Collection $departmentIds, string $theme): int
    {
        if ($departmentIds->isEmpty()) {
            return 0;
        }

        return Report::whereIn('department_id', $departmentIds)
            ->whereNotIn('status', ['approved', 'rejected'])
            ->forTheme($theme)
            ->count();
    }

    /**
     * @param  Collection<int, int|string>  $departmentIds
     */
    private function closedReportCount(Collection $departmentIds, ?string $theme): int
    {
        if ($departmentIds->isEmpty()) {
            return 0;
        }

        return $this->reportQuery($departmentIds, $theme)
            ->whereIn('status', ['approved', 'rejected'])
            ->count();
    }

    private function overdueCapaQuery(?int $departmentId)
    {
        return Capa::where('company_id', $this->companyId)
            ->where('status', '!=', 'closed')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now()->toDateString())
            ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId));
    }

    /**
     * @param  Collection<int, int|string>  $departmentIds
     * @return array<int, int>
     */
    private function countsByDepartment(Collection $departmentIds, string $theme, string $state): array
    {
        if ($departmentIds->isEmpty()) {
            return [];
        }

        $query = Report::query()
            ->whereIn('department_id', $departmentIds)
            ->forTheme($theme);

        if ($state === 'open') {
            $query->whereNotIn('status', ['approved', 'rejected']);
        }

        return $query
            ->selectRaw('department_id, COUNT(*) as cnt')
            ->groupBy('department_id')
            ->pluck('cnt', 'department_id')
            ->mapWithKeys(fn ($count, $id) => [(int) $id => (int) $count])
            ->all();
    }

    /**
     * @param  Collection<int, int|string>  $departmentIds
     * @return array<int, int>
     */
    private function overdueCapasByDepartment(Collection $departmentIds): array
    {
        if ($departmentIds->isEmpty()) {
            return [];
        }

        return Capa::query()
            ->where('company_id', $this->companyId)
            ->where('status', '!=', 'closed')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now()->toDateString())
            ->whereIn('department_id', $departmentIds)
            ->selectRaw('department_id, COUNT(*) as cnt')
            ->groupBy('department_id')
            ->pluck('cnt', 'department_id')
            ->mapWithKeys(fn ($count, $id) => [(int) $id => (int) $count])
            ->all();
    }

    /**
     * Departments tied for the highest report volume, or the top five when
     * more than five departments have reports. A single-department company
     * gets no heat marker.
     *
     * @param  Collection<int, Department>  $departments
     * @return list<int>
     */
    private function heatDepartmentIds(Collection $departments): array
    {
        if ($departments->count() < 2) {
            return [];
        }

        $ranked = Report::query()
            ->whereIn('department_id', $departments->pluck('id'))
            ->selectRaw('department_id, COUNT(*) as cnt')
            ->groupBy('department_id')
            ->pluck('cnt', 'department_id')
            ->mapWithKeys(fn ($count, $id) => [(int) $id => (int) $count])
            ->filter(fn ($count) => $count > 0)
            ->sortDesc();

        if ($ranked->isEmpty()) {
            return [];
        }

        if ($ranked->count() > 5) {
            return $ranked->take(5)->keys()->map(fn ($id) => (int) $id)->values()->all();
        }

        $highest = (int) $ranked->first();

        return $ranked
            ->filter(fn ($count) => (int) $count === $highest)
            ->keys()
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }
}

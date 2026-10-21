<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DefaultDepartment;
use App\Models\FormTemplate;
use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class DefaultDepartmentSettingsController extends Controller
{
    public function index(Request $request)
    {
        $sectors = Sector::query()
            ->with('industry:id,name')
            ->orderBy('name')
            ->get();

        $counts = DefaultDepartment::query()
            ->selectRaw('sector_id, COUNT(*) as departments_count')
            ->groupBy('sector_id')
            ->pluck('departments_count', 'sector_id');

        $sectorId = $request->integer('sector_id');
        if (! $sectors->contains('id', $sectorId)) {
            $sectorId = $sectors->first()?->id;
        }

        $departments = DefaultDepartment::query()
            ->when($sectorId, fn ($query) => $query->where('sector_id', $sectorId))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (DefaultDepartment $department) => [
                'id' => $department->id,
                'sector_id' => $department->sector_id,
                'name' => $department->name,
                'description' => $department->description,
                'library_key' => $department->library_key,
                'library_label' => $this->libraryLabel($department->library_key),
                'sort_order' => $department->sort_order,
            ])
            ->values();

        return Inertia::render('Admin/Settings/Index', [
            'sectors' => $sectors->map(fn (Sector $sector) => [
                'id' => $sector->id,
                'name' => $sector->name,
                'industry' => $sector->industry?->name,
                'departments_count' => (int) ($counts[$sector->id] ?? 0),
            ])->values(),
            'sectorId' => $sectorId,
            'departments' => $departments,
            'libraryGroups' => $this->libraryGroups($sectorId),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['sort_order'] = (int) DefaultDepartment::query()
            ->where('sector_id', $data['sector_id'])
            ->max('sort_order') + 1;

        DefaultDepartment::create($data);

        return redirect()
            ->route('settings.index', ['sector_id' => $data['sector_id']])
            ->with('success', 'Default department added.');
    }

    public function update(Request $request, DefaultDepartment $defaultDepartment)
    {
        $data = $this->validated($request, $defaultDepartment);
        unset($data['sector_id']);

        $defaultDepartment->update($data);

        return redirect()
            ->route('settings.index', ['sector_id' => $defaultDepartment->sector_id])
            ->with('success', 'Default department updated.');
    }

    public function destroy(DefaultDepartment $defaultDepartment)
    {
        $sectorId = $defaultDepartment->sector_id;
        $defaultDepartment->delete();

        return redirect()
            ->route('settings.index', ['sector_id' => $sectorId])
            ->with('success', 'Default department removed.');
    }

    public function move(Request $request, DefaultDepartment $defaultDepartment)
    {
        $request->validate([
            'direction' => ['required', Rule::in(['up', 'down'])],
        ]);

        $items = DefaultDepartment::query()
            ->where('sector_id', $defaultDepartment->sector_id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->values();

        $index = $items->search(fn (DefaultDepartment $item) => $item->id === $defaultDepartment->id);
        $swapWith = $request->input('direction') === 'up' ? $index - 1 : $index + 1;

        if ($index !== false && $swapWith >= 0 && $swapWith < $items->count()) {
            $ordered = $items->all();
            [$ordered[$index], $ordered[$swapWith]] = [$ordered[$swapWith], $ordered[$index]];

            foreach (array_values($ordered) as $position => $item) {
                if ((int) $item->sort_order !== $position + 1) {
                    $item->update(['sort_order' => $position + 1]);
                }
            }
        }

        return redirect()->route('settings.index', ['sector_id' => $defaultDepartment->sector_id]);
    }

    /**
     * @return array{sector_id: int, name: string, description: ?string, library_key: ?string}
     */
    private function validated(Request $request, ?DefaultDepartment $existing = null): array
    {
        $sectorId = $existing?->sector_id ?? (int) $request->input('sector_id');

        $validated = $request->validate([
            'sector_id' => [$existing ? 'nullable' : 'required', 'integer', 'exists:sectors,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('default_departments', 'name')
                    ->where(fn ($query) => $query->where('sector_id', $sectorId))
                    ->ignore($existing?->id),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'library_key' => ['nullable', 'string', 'max:255'],
        ]);

        $libraryKey = trim((string) ($validated['library_key'] ?? ''));
        $libraryKey = $libraryKey === '' ? null : $libraryKey;

        if ($libraryKey !== null && $libraryKey !== $existing?->library_key) {
            $allowed = FormTemplate::query()
                ->where('library_key', $libraryKey)
                ->whereHas('sectors', fn ($query) => $query->where('sectors.id', $sectorId))
                ->exists();

            if (! $allowed) {
                throw ValidationException::withMessages([
                    'library_key' => 'Choose a form group linked to this sector.',
                ]);
            }
        }

        return [
            'sector_id' => $sectorId,
            'name' => trim($validated['name']),
            'description' => trim((string) ($validated['description'] ?? '')) ?: null,
            'library_key' => $libraryKey,
        ];
    }

    private function libraryGroups(?int $sectorId)
    {
        if (! $sectorId) {
            return [];
        }

        return FormTemplate::query()
            ->whereHas('sectors', fn ($query) => $query->where('sectors.id', $sectorId))
            ->whereNotNull('library_key')
            ->where('library_key', '!=', '')
            ->selectRaw('library_key, COUNT(*) as templates_count')
            ->groupBy('library_key')
            ->orderBy('library_key')
            ->get()
            ->map(fn ($row) => [
                'key' => $row->library_key,
                'label' => $this->libraryLabel($row->library_key),
                'templates_count' => (int) $row->templates_count,
            ])
            ->values();
    }

    private function libraryLabel(?string $key): string
    {
        if ($key === null || $key === '') {
            return 'No form group';
        }

        if (str_contains($key, ' ')) {
            return $key;
        }

        return Str::title(str_replace('-', ' ', $key));
    }
}

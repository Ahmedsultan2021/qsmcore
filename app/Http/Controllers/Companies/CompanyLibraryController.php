<?php

namespace App\Http\Controllers\Companies;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LibraryCategory;
use App\Models\LibraryDocument;
use App\Models\LibraryFavorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CompanyLibraryController extends Controller
{
    private function companyId(): int
    {
        return (int) Auth::guard('employee')->user()->company_id;
    }

    private function employeeId(): int
    {
        return (int) Auth::guard('employee')->user()->id;
    }

    private function authorizeDocument(LibraryDocument $library): void
    {
        if ((int) $library->company_id !== $this->companyId()) {
            abort(403);
        }

        if ($library->status === LibraryDocument::STATUS_DRAFT) {
            $employeeId = $this->employeeId();
            if ((int) $library->uploaded_by !== $employeeId && (int) $library->owner_employee_id !== $employeeId) {
                abort(403);
            }
        }
    }

    private function authorizeCompanyDocument(LibraryDocument $library): void
    {
        if ((int) $library->company_id !== $this->companyId()) {
            abort(403);
        }
    }

    private function baseQuery()
    {
        return LibraryDocument::where('company_id', $this->companyId())
            ->visibleToEmployee($this->employeeId());
    }

    private function documentValidationRules(bool $requireFile = true): array
    {
        $rules = [
            'title'                 => 'required|string|max:255',
            'document_code'         => 'nullable|string|max:64',
            'version_label'         => 'nullable|string|max:64',
            'description'           => 'nullable|string|max:2000',
            'library_category_id'   => 'nullable|exists:library_categories,id',
            'owner_employee_id'     => 'nullable|exists:employees,id',
            'effective_date'        => 'nullable|date',
            'status'                => 'required|in:draft,under_review,effective',
        ];

        if ($requireFile) {
            $rules['file'] = 'required|file|mimes:pdf,doc,docx,xls,xlsx,csv,ppt,pptx,jpg,jpeg,png|max:20480';
        } else {
            $rules['file'] = 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,csv,ppt,pptx,jpg,jpeg,png|max:20480';
        }

        return $rules;
    }

    private function validateCategoryAndOwner(Request $request, array &$validated): void
    {
        if (! empty($validated['library_category_id'])) {
            $exists = LibraryCategory::where('company_id', $this->companyId())
                ->where('id', $validated['library_category_id'])
                ->exists();
            if (! $exists) {
                abort(422, 'Invalid category.');
            }
        }

        if (! empty($validated['owner_employee_id'])) {
            $exists = Employee::where('company_id', $this->companyId())
                ->where('id', $validated['owner_employee_id'])
                ->exists();
            if (! $exists) {
                abort(422, 'Invalid owner.');
            }
        }
    }

    private function mapDocument(LibraryDocument $doc, array $favoriteIds): array
    {
        $data = $doc->toArray();
        $data['is_favorited'] = in_array($doc->id, $favoriteIds, true);
        $data['download_url'] = route('companies.library.download', $doc->id);
        $data['view_url'] = route('companies.library.view', $doc->id);

        return $data;
    }

    public function index(Request $request)
    {
        $companyId = $this->companyId();
        $employeeId = $this->employeeId();

        LibraryCategory::ensureDefaultsForCompany($companyId);

        $search = $request->string('search')->trim()->toString();
        $categoryId = $request->input('category_id');
        $status = $request->input('status');
        $favoritesOnly = $request->boolean('favorites');

        $statsBase = $this->baseQuery();
        $stats = [
            'total'         => (clone $statsBase)->count(),
            'drafts'        => (clone $statsBase)->where('status', LibraryDocument::STATUS_DRAFT)->count(),
            'under_review'  => (clone $statsBase)->where('status', LibraryDocument::STATUS_UNDER_REVIEW)->count(),
            'effective'     => (clone $statsBase)->where('status', LibraryDocument::STATUS_EFFECTIVE)->count(),
        ];

        $query = $this->baseQuery()
            ->with([
                'uploader:id,fname,lname',
                'owner:id,fname,lname',
                'category:id,name',
            ]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('document_code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($categoryId !== null && $categoryId !== '' && $categoryId !== 'all') {
            if ($categoryId === 'uncategorized') {
                $query->whereNull('library_category_id');
            } else {
                $query->where('library_category_id', (int) $categoryId);
            }
        }

        if ($status && in_array($status, LibraryDocument::STATUSES, true)) {
            $query->where('status', $status);
        }

        if ($favoritesOnly) {
            $query->whereHas('favorites', fn ($q) => $q->where('employee_id', $employeeId));
        }

        $documents = $query->latest()->paginate(15)->withQueryString();

        $favoriteIds = LibraryFavorite::where('employee_id', $employeeId)
            ->pluck('library_document_id')
            ->all();

        $documents->getCollection()->transform(
            fn (LibraryDocument $doc) => $this->mapDocument($doc, $favoriteIds)
        );

        $categories = LibraryCategory::where('company_id', $companyId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(function (LibraryCategory $cat) use ($companyId, $employeeId) {
                $count = LibraryDocument::where('company_id', $companyId)
                    ->where('library_category_id', $cat->id)
                    ->visibleToEmployee($employeeId)
                    ->count();

                return [
                    'id'    => $cat->id,
                    'name'  => $cat->name,
                    'count' => $count,
                ];
            });

        $uncategorizedCount = LibraryDocument::where('company_id', $companyId)
            ->whereNull('library_category_id')
            ->visibleToEmployee($employeeId)
            ->count();

        return Inertia::render('Companies/Library/Index', [
            'documents'           => $documents,
            'categories'          => $categories,
            'uncategorized_count' => $uncategorizedCount,
            'stats'               => $stats,
            'filters'             => [
                'search'      => $search,
                'category_id' => $categoryId,
                'status'      => $status,
                'favorites'   => $favoritesOnly,
            ],
            'statuses' => [
                ['value' => LibraryDocument::STATUS_EFFECTIVE, 'label' => 'Effective'],
                ['value' => LibraryDocument::STATUS_UNDER_REVIEW, 'label' => 'Under Review'],
                ['value' => LibraryDocument::STATUS_DRAFT, 'label' => 'Draft'],
            ],
        ]);
    }

    public function create()
    {
        $companyId = $this->companyId();
        LibraryCategory::ensureDefaultsForCompany($companyId);

        return Inertia::render('Companies/Library/Create', [
            'categories' => $this->formOptions($companyId),
            'employees'  => $this->employeesList($companyId),
            'statuses'   => $this->statusOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $authEmployee = Auth::guard('employee')->user();

        $validated = $request->validate($this->documentValidationRules(true));
        $this->validateCategoryAndOwner($request, $validated);

        $file = $request->file('file');
        $filePath = $file->store('library-documents/' . $authEmployee->company_id, 'public');

        LibraryDocument::create([
            'company_id'          => $authEmployee->company_id,
            'uploaded_by'         => $authEmployee->id,
            'library_category_id' => $validated['library_category_id'] ?? null,
            'owner_employee_id'   => $validated['owner_employee_id'] ?? $authEmployee->id,
            'title'               => $validated['title'],
            'document_code'       => $validated['document_code'] ?? null,
            'version_label'       => $validated['version_label'] ?? null,
            'description'         => $validated['description'] ?? null,
            'effective_date'      => $validated['effective_date'] ?? null,
            'status'              => $validated['status'],
            'file_path'           => $filePath,
            'original_name'       => $file->getClientOriginalName(),
            'mime_type'           => $file->getClientMimeType(),
            'file_size'           => $file->getSize(),
        ]);

        return redirect()->route('companies.library.index')
            ->with('success', 'Document uploaded successfully.');
    }

    public function edit(LibraryDocument $library)
    {
        $this->authorizeCompanyDocument($library);

        LibraryCategory::ensureDefaultsForCompany($this->companyId());

        $doc = $library->load(['category:id,name', 'owner:id,fname,lname']);

        return Inertia::render('Companies/Library/Edit', [
            'document'   => $doc,
            'categories' => $this->formOptions($this->companyId()),
            'employees'  => $this->employeesList($this->companyId()),
            'statuses'   => $this->statusOptions(),
        ]);
    }

    public function update(Request $request, LibraryDocument $library)
    {
        $this->authorizeCompanyDocument($library);

        $authEmployee = Auth::guard('employee')->user();

        $validated = $request->validate($this->documentValidationRules(false));
        $this->validateCategoryAndOwner($request, $validated);

        $data = [
            'title'               => $validated['title'],
            'document_code'       => $validated['document_code'] ?? null,
            'version_label'       => $validated['version_label'] ?? null,
            'description'         => $validated['description'] ?? null,
            'library_category_id' => $validated['library_category_id'] ?? null,
            'owner_employee_id'   => $validated['owner_employee_id'] ?? null,
            'effective_date'      => $validated['effective_date'] ?? null,
            'status'              => $validated['status'],
        ];

        if ($request->hasFile('file')) {
            if ($library->file_path && Storage::disk('public')->exists($library->file_path)) {
                Storage::disk('public')->delete($library->file_path);
            }
            $file = $request->file('file');
            $data['file_path']     = $file->store('library-documents/' . $authEmployee->company_id, 'public');
            $data['original_name'] = $file->getClientOriginalName();
            $data['mime_type']     = $file->getClientMimeType();
            $data['file_size']     = $file->getSize();
        }

        $library->update($data);

        return redirect()->route('companies.library.index')
            ->with('success', 'Document updated successfully.');
    }

    public function destroy(LibraryDocument $library)
    {
        $this->authorizeCompanyDocument($library);

        if ($library->file_path && Storage::disk('public')->exists($library->file_path)) {
            Storage::disk('public')->delete($library->file_path);
        }

        $library->delete();

        return redirect()->route('companies.library.index')
            ->with('success', 'Document deleted successfully.');
    }

    public function download(LibraryDocument $library): StreamedResponse
    {
        $this->authorizeDocument($library);

        if (! $library->file_path || ! Storage::disk('public')->exists($library->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($library->file_path, $library->original_name);
    }

    public function view(LibraryDocument $library): StreamedResponse
    {
        $this->authorizeDocument($library);

        if (! $library->file_path || ! Storage::disk('public')->exists($library->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->response(
            $library->file_path,
            $library->original_name,
            ['Content-Type' => $library->mime_type ?: 'application/octet-stream']
        );
    }

    public function toggleFavorite(LibraryDocument $library)
    {
        $this->authorizeDocument($library);

        $employeeId = $this->employeeId();

        $existing = LibraryFavorite::where('employee_id', $employeeId)
            ->where('library_document_id', $library->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $favorited = false;
        } else {
            LibraryFavorite::create([
                'employee_id'         => $employeeId,
                'library_document_id' => $library->id,
            ]);
            $favorited = true;
        }

        return back()->with('success', $favorited ? 'Added to favorites.' : 'Removed from favorites.');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $companyId = $this->companyId();

        LibraryCategory::firstOrCreate(
            ['company_id' => $companyId, 'name' => $validated['name']],
            ['sort_order' => (int) LibraryCategory::where('company_id', $companyId)->max('sort_order') + 1]
        );

        return redirect()->route('companies.library.index')
            ->with('success', 'Category added.');
    }

    private function formOptions(int $companyId): array
    {
        return LibraryCategory::where('company_id', $companyId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->toArray();
    }

    private function employeesList(int $companyId): array
    {
        return Employee::where('company_id', $companyId)
            ->orderBy('fname')
            ->orderBy('lname')
            ->get(['id', 'fname', 'lname'])
            ->map(fn ($e) => [
                'id'   => $e->id,
                'name' => trim($e->fname . ' ' . $e->lname),
            ])
            ->toArray();
    }

    private function statusOptions(): array
    {
        return [
            ['value' => LibraryDocument::STATUS_EFFECTIVE, 'label' => 'Effective'],
            ['value' => LibraryDocument::STATUS_UNDER_REVIEW, 'label' => 'Under Review'],
            ['value' => LibraryDocument::STATUS_DRAFT, 'label' => 'Draft'],
        ];
    }
}

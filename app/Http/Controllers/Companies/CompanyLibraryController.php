<?php

namespace App\Http\Controllers\Companies;

use App\Http\Controllers\Controller;
use App\Models\LibraryDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CompanyLibraryController extends Controller
{
    public function index()
    {
        $authEmployee = Auth::guard('employee')->user();

        $documents = LibraryDocument::where('company_id', (int) $authEmployee->company_id)
            ->with('uploader:id,fname,lname')
            ->latest()
            ->paginate(15);

        return Inertia::render('Companies/Library/Index', [
            'documents' => $documents,
        ]);
    }

    public function create()
    {
        return Inertia::render('Companies/Library/Create');
    }

    public function store(Request $request)
    {
        $authEmployee = Auth::guard('employee')->user();

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'file'        => 'required|file|mimes:pdf,doc,docx,xls,xlsx,csv,ppt,pptx,jpg,jpeg,png|max:20480',
        ]);

        $file = $request->file('file');
        $filePath = $file->store('library-documents/' . $authEmployee->company_id, 'public');

        LibraryDocument::create([
            'company_id'    => $authEmployee->company_id,
            'uploaded_by'   => $authEmployee->id,
            'title'         => $validated['title'],
            'description'   => $validated['description'] ?? null,
            'file_path'     => $filePath,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getClientMimeType(),
            'file_size'     => $file->getSize(),
        ]);

        return redirect()->route('companies.library.index')
            ->with('success', 'Document uploaded successfully.');
    }

    public function edit(LibraryDocument $library)
    {
        $authEmployee = Auth::guard('employee')->user();

        if ((int) $library->company_id !== (int) $authEmployee->company_id) {
            abort(403);
        }

        if ((int) $library->uploaded_by !== (int) $authEmployee->id) {
            abort(403, 'You can only edit documents you uploaded.');
        }

        return Inertia::render('Companies/Library/Edit', [
            'document' => $library,
        ]);
    }

    public function update(Request $request, LibraryDocument $library)
    {
        $authEmployee = Auth::guard('employee')->user();

        if ((int) $library->company_id !== (int) $authEmployee->company_id) {
            abort(403);
        }

        if ((int) $library->uploaded_by !== (int) $authEmployee->id) {
            abort(403, 'You can only edit documents you uploaded.');
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'file'        => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,csv,ppt,pptx,jpg,jpeg,png|max:20480',
        ]);

        $data = [
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
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
        $authEmployee = Auth::guard('employee')->user();

        if ((int) $library->company_id !== (int) $authEmployee->company_id) {
            abort(403);
        }

        if ((int) $library->uploaded_by !== (int) $authEmployee->id) {
            abort(403, 'You can only delete documents you uploaded.');
        }

        if ($library->file_path && Storage::disk('public')->exists($library->file_path)) {
            Storage::disk('public')->delete($library->file_path);
        }

        $library->delete();

        return redirect()->route('companies.library.index')
            ->with('success', 'Document deleted successfully.');
    }
}

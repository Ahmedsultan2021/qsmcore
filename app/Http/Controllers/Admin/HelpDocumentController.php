<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HelpDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class HelpDocumentController extends Controller
{
    public function index()
    {
        $documents = HelpDocument::orderBy('sort_order')
            ->latest('id')
            ->paginate(15);

        return Inertia::render('Admin/HelpDocuments/Index', [
            'documents' => $documents,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/HelpDocuments/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'file'        => 'required|file|mimes:pdf,doc,docx,xls,xlsx,csv|max:20480',
            'is_active'   => 'boolean',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        $file = $request->file('file');
        $filePath = $file->store('help-documents', 'public');

        HelpDocument::create([
            'title'         => $validated['title'],
            'description'   => $validated['description'] ?? null,
            'file_path'     => $filePath,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getClientMimeType(),
            'file_size'     => $file->getSize(),
            'is_active'     => $validated['is_active'] ?? true,
            'sort_order'    => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('help-documents.index')
            ->with('success', 'Help document uploaded successfully.');
    }

    public function edit(HelpDocument $helpDocument)
    {
        return Inertia::render('Admin/HelpDocuments/Edit', [
            'document' => $helpDocument,
        ]);
    }

    public function update(Request $request, HelpDocument $helpDocument)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'file'        => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,csv|max:20480',
            'is_active'   => 'boolean',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        $data = [
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_active'   => $validated['is_active'] ?? false,
            'sort_order'  => $validated['sort_order'] ?? 0,
        ];

        if ($request->hasFile('file')) {
            if ($helpDocument->file_path && Storage::disk('public')->exists($helpDocument->file_path)) {
                Storage::disk('public')->delete($helpDocument->file_path);
            }
            $file = $request->file('file');
            $data['file_path']     = $file->store('help-documents', 'public');
            $data['original_name'] = $file->getClientOriginalName();
            $data['mime_type']     = $file->getClientMimeType();
            $data['file_size']     = $file->getSize();
        }

        $helpDocument->update($data);

        return redirect()->route('help-documents.index')
            ->with('success', 'Help document updated successfully.');
    }

    public function destroy(HelpDocument $helpDocument)
    {
        if ($helpDocument->file_path && Storage::disk('public')->exists($helpDocument->file_path)) {
            Storage::disk('public')->delete($helpDocument->file_path);
        }
        $helpDocument->delete();

        return redirect()->route('help-documents.index')
            ->with('success', 'Help document deleted successfully.');
    }
}

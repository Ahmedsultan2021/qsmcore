<?php

namespace App\Http\Controllers\Companies;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Report;
use App\Models\ReportFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CompanyReportFileController extends Controller
{
    private function authorizeDepartment(Department $department): void
    {
        $employee = Auth::guard('employee')->user();
        if ((int) $department->company_id !== (int) $employee->company_id) {
            abort(403);
        }
    }

    public function store(Request $request, Department $department, Report $report)
    {
        $this->authorizeDepartment($department);
        if ($report->department_id !== $department->id) {
            abort(404);
        }

        $request->validate([
            'file' => 'sometimes|file|max:25600',
            'files' => 'sometimes|array|max:30',
            'files.*' => 'file|max:25600',
        ]);

        if ($request->hasFile('files')) {
            $uploads = $request->file('files');
        } elseif ($request->hasFile('file')) {
            $uploads = [$request->file('file')];
        } else {
            return redirect()->route('companies.departments.reports.show', [
                'department' => $department->id,
                'report' => $report->id,
            ])->withErrors(['file' => 'Please choose at least one file.']);
        }

        $count = 0;
        foreach ($uploads as $uploaded) {
            if (! $uploaded) {
                continue;
            }
            $path = $uploaded->store("reports/{$report->id}", 'public');
            ReportFile::create([
                'report_id' => $report->id,
                'uploaded_by' => Auth::guard('employee')->id(),
                'original_name' => $uploaded->getClientOriginalName(),
                'path' => $path,
                'size' => $uploaded->getSize(),
                'mime_type' => $uploaded->getClientMimeType(),
            ]);
            $count++;
        }

        $message = $count === 1
            ? 'File uploaded successfully'
            : "{$count} files uploaded successfully";

        return redirect()->route('companies.departments.reports.show', [
            'department' => $department->id,
            'report' => $report->id,
        ])->with('success', $message);
    }

    public function download(Department $department, Report $report, ReportFile $reportFile): StreamedResponse
    {
        $this->authorizeDepartment($department);
        if ($report->department_id !== $department->id || $reportFile->report_id !== $report->id) {
            abort(404);
        }

        if (! Storage::disk('public')->exists($reportFile->path)) {
            abort(404);
        }

        return Storage::disk('public')->download($reportFile->path, $reportFile->original_name);
    }

    public function destroy(Department $department, Report $report, ReportFile $reportFile)
    {
        $this->authorizeDepartment($department);
        if ($report->department_id !== $department->id || $reportFile->report_id !== $report->id) {
            abort(404);
        }

        Storage::disk('public')->delete($reportFile->path);
        $reportFile->delete();

        return redirect()->route('companies.departments.reports.show', [
            'department' => $department->id,
            'report' => $report->id,
        ])->with('success', 'File removed');
    }
}

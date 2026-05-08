<?php

namespace App\Exports;

use App\Models\Report;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CompanyReportsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    public function __construct(private int $companyId, private ?int $departmentId = null)
    {
    }

    public function collection(): Collection
    {
        $query = Report::query()
            ->with(['department', 'creator'])
            ->whereHas('department', fn ($q) => $q->where('company_id', $this->companyId))
            ->latest();

        if ($this->departmentId) {
            $query->where('department_id', $this->departmentId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['#', 'Title', 'Department', 'Status', 'Report Date', 'Created By', 'Created At'];
    }

    public function map($report): array
    {
        return [
            $report->id,
            $report->title,
            $report->department?->name ?? '-',
            ucfirst($report->status ?? '-'),
            optional($report->report_date)->format('Y-m-d') ?? '-',
            $report->creator?->name ?? '-',
            optional($report->created_at)->format('Y-m-d H:i') ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Reports';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '059669'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 35,
            'C' => 25,
            'D' => 14,
            'E' => 14,
            'F' => 22,
            'G' => 18,
        ];
    }
}

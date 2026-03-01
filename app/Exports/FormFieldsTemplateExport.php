<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class FormFieldsTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    public function array(): array
    {
        // Return comprehensive example rows showing all field types
        return [
            // Text field example
            [
                'text',
                'Full Name',
                'full_name',
                'Enter your full name',
                '1',
                '',
            ],
            // Email field example
            [
                'email',
                'Email Address',
                'email_address',
                'Enter your email address',
                '1',
                '',
            ],
            // Number field example
            [
                'number',
                'Age',
                'age',
                'Enter your age',
                '0',
                '',
            ],
            // Textarea field example
            [
                'textarea',
                'Comments',
                'comments',
                'Enter your comments here',
                '0',
                '',
            ],
            // Date field example
            [
                'date',
                'Date of Birth',
                'date_of_birth',
                'Select your date of birth',
                '1',
                '',
            ],
            // Select dropdown example
            [
                'select',
                'Country',
                'country',
                'Select your country',
                '1',
                'USA,Canada,Mexico,UK,Germany,France,Spain,Italy',
            ],
            // Radio buttons example
            [
                'radio',
                'Gender',
                'gender',
                'Select your gender',
                '1',
                'Male,Female,Other,Prefer not to say',
            ],
            // Checkboxes example
            [
                'checkbox',
                'Interests',
                'interests',
                'Select all that apply',
                '0',
                'Sports,Music,Reading,Travel,Cooking,Technology',
            ],
            // File upload example
            [
                'file',
                'Upload Resume',
                'resume',
                'Upload your resume (PDF, DOC, DOCX)',
                '1',
                '',
            ],
            // Signature example
            [
                'signature',
                'Signature',
                'signature',
                'Sign in the box below',
                '1',
                '',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Field Type',
            'Label',
            'Field Name',
            'Placeholder',
            'Required (1=Yes, 0=No)',
            'Options (comma-separated for select/radio/checkbox)',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4285F4'], // Google Blue
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
            'A' => 15,  // Field Type
            'B' => 25,  // Label
            'C' => 20,  // Field Name
            'D' => 30,  // Placeholder
            'E' => 20,  // Required
            'F' => 50,  // Options
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Add instructions row after headings
                $sheet->insertNewRowBefore(2, 1);
                $sheet->setCellValue('A2', 'Available Field Types:');
                $sheet->mergeCells('A2:F2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E8F0FE'], // Light blue
                    ],
                ]);

                // Add field types guide
                $sheet->setCellValue('A3', 'text, textarea, email, number, date, select, radio, checkbox, file, signature');
                $sheet->mergeCells('A3:F3');
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['size' => 10],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F1F3F4'], // Light gray
                    ],
                ]);

                // Add instructions for options column
                $sheet->setCellValue('A4', 'Note: Options column is only required for select, radio, and checkbox field types. Use comma to separate multiple options.');
                $sheet->mergeCells('A4:F4');
                $sheet->getStyle('A4')->applyFromArray([
                    'font' => ['size' => 9, 'italic' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFF9E6'], // Light yellow
                    ],
                ]);

                // Add instructions for field name
                $sheet->setCellValue('A5', 'Field Name must be lowercase with underscores only (e.g., first_name, email_address). If left empty, it will be auto-generated from the label.');
                $sheet->mergeCells('A5:F5');
                $sheet->getStyle('A5')->applyFromArray([
                    'font' => ['size' => 9, 'italic' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFF9E6'], // Light yellow
                    ],
                ]);

                // Style the example data rows
                $lastRow = $sheet->getHighestRow();
                for ($row = 7; $row <= $lastRow; $row++) {
                    $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'E0E0E0'],
                            ],
                        ],
                    ]);
                    
                    // Alternate row colors for better readability
                    if ($row % 2 == 0) {
                        $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'FAFAFA'],
                            ],
                        ]);
                    }
                }

                // Add border to instructions area
                $sheet->getStyle('A2:F5')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);
            },
        ];
    }
}


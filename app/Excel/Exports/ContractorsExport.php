<?php

namespace App\Excel\Exports;

use App\Contractor;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ContractorsExport implements WithMultipleSheets
{
    // Create multiple sheet
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new ContractorRegistered();

        
        return $sheets;
    }
}

class ContractorRegistered implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithEvents, WithColumnFormatting
{

    public function collection()
    {
        return Contractor::with('user')->get();
    }

    public function title(): string
    {
        return 'Contractors';
    }

    // Select data from query and set its position
    public function map($contractors): array
    {
        return [
            $contractors->user->name,
            $contractors->user->email,
            $contractors->contact_no,
            Date::dateTimeToExcel($contractors->created_at),
        ];
    }
    // Set Date Format
    public function columnFormats(): array
    {
        return [
            'D' => 'dd mmm yyyy',
        ];
    }

    // Add heading for columns
    public function headings(): array
    {
        return [
            'Contractor',
            'Email',
            'Contact No.',
            'Associated Date',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:D1')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);
            },
        ];
    }
}

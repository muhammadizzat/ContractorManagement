<?php

namespace App\Excel\Exports;

use App\UnitType;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

use App\Excel\Sheets\ProjectDetailsSheet;

class UnitTypesExport implements WithMultipleSheets
{
    protected $proj_id;
    
    // Get arguement when pass from function called
    public function __construct(int $proj_id)
    {
        $this->proj_id = $proj_id;
    }

    // Create multiple sheet
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new UnitTypesSheet($this->proj_id);
        $sheets[] = new ProjectDetailsSheet($this->proj_id);
        
        return $sheets;
    }
}

class UnitTypesSheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithEvents, WithColumnFormatting
{
    protected $proj_id;

    public function __construct($proj_id)
    {
       $this->proj_id = $proj_id;
    }


    public function collection()
    {
        return UnitType::where('project_id', $this->proj_id)->get();
    }

    public function title(): string
    {
        return 'Unit Types';
    }

    // Select data from query and set its position
    public function map($unit_type): array
    {
        return [
            $unit_type->name,
            Date::dateTimeToExcel($unit_type->created_at),

        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => 'dd mmm yyyy',
        ];
    }
    // Add heading for columns
    public function headings(): array
    {
        return [
            'Name',
            'Created At',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:E1')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);
            },
        ];
    }
}
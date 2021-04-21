<?php

namespace App\Excel\Exports;

use App\Unit;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

use App\Excel\Sheets\ProjectDetailsSheet;

class UnitsExport implements WithMultipleSheets
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

        $sheets[] = new UnitsSheet($this->proj_id);
        $sheets[] = new ProjectDetailsSheet($this->proj_id);

        return $sheets;
    }
}

class UnitsSheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithEvents
{
    protected $proj_id;

    public function __construct($proj_id)
    {
       $this->proj_id = $proj_id;
    }


    public function collection()
    {
        return Unit::with('unit_type')
                    ->where('project_id', $this->proj_id)
                    ->get();
    }

    public function title(): string
    {
        return 'Units';
    }

    // Select data from query and set its position
    public function map($unit): array
    {
        return [
            $unit->unit_no,
            $unit->unit_type->name,
            $unit->owner_name,
            $unit->owner_email,
            $unit->owner_contact_no
        ];
    }

    // Add heading for columns
    public function headings(): array
    {
        return [
            'Unit No',
            'Unit Type',
            'Owner Name',
            'Owner Email',
            'Contact No',
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
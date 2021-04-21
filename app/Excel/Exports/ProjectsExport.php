<?php

namespace App\Excel\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class ProjectsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithEvents, WithColumnFormatting
{
    
    protected $primary_admin;
    
    // Get arguement when pass from function called
    public function __construct($projects)
    {
        $this->projects = $projects;
    }

    public function collection()
    {
        $projects = $this->projects;
        return $projects;
    }

    public function title(): string
    {
        return 'Projects';
    }

    // Select data from query and set its position
    public function map($project): array
    {
        return [
            $project->name,
            $project->developer_projects->name,
            $project->status,
            $project->description,
            Date::dateTimeToExcel($project->created_at),
        ];
    }
    // Set Date Format
    public function columnFormats(): array
    {
        return [
            'E' => 'dd mmm yyyy',
            'F' => 'dd mmm yyyy',
            'G' => 'dd mmm yyyy',
        ];
    }

    // Add heading for columns
    public function headings(): array
    {
        return [
            'Name',
            'Developer Name',
            'Status',
            'Description',
            'Created At',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:G1')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);
            },
        ];
    }
}
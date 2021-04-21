<?php

namespace App\Excel\Sheets;

use App\Project;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ProjectDetailsSheet implements FromCollection, ShouldAutoSize, WithTitle, WithEvents
{
    protected $proj_id;

    public function __construct($proj_id)
    {
       $this->proj_id = $proj_id;
    }

    public function collection()
    {
        $project = Project::with('developer_projects')
                ->where('id', $this->proj_id)
                ->first();
        
        // Create custom data array
        return collect([
            [
                'title' => 'Developer Name',
                'detail' => $project->developer_projects->name,
                
            ],
            [
                'title' => 'Project Name',
                'detail' => $project->name,
                
            ]
        ]);
    }

    // Set the title of the sheet
    public function title(): string
    {
        return 'Project Details';
    }

    // Set Column/Row Styling after sheet has been draw
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:A2')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);
            },
        ];
    } 
}
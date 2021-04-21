<?php

namespace App\Excel\Sheets;

use App\ProjectCase;
use App\Constants\DefectStatus;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;


class CaseDetailsSheet implements FromCollection, ShouldAutoSize, WithTitle, WithEvents
{
    protected $id;

    public function __construct($id)
    {
       $this->id = $id;
    }

    public function collection()
    {
        $case = ProjectCase::with(['project', 'assigned_cow', 'unit', 'tags'])
                ->where('id', $this->id)->first();
        if($case->assigned_cow){
            $cowName = $case->assigned_cow->name;
        } else {
            $cowName = '';
        }
        
        // Create custom data array
        return collect([
            [
                'title' => 'Case No.',
                'detail' => "C".$case->ref_no,  
            ],
            [
                'title' => 'Title',
                'detail' => $case->title,  
            ],
            [
                'title' => 'Project',
                'detail' => $case->project->name,  
            ],
            [
                'title' => 'Developer',
                'detail' => $case->project->developer_projects->name,  
            ],
            [
                'title' => 'Assigned CoW',
                'detail' => $cowName,  
            ],
            [
                'title' => 'Unit No.',
                'detail' => ' '.$case->unit->unit_no,  
            ],
            [
                'title' => 'Created Date',
                'detail' => $case->created_at->format('d M Y'),  
            ],
            [
                'title' => 'Status',
                'detail' => DefectStatus::$dict[$case->status],
            ]
        ]);
    }

    // Set the title of the sheet
    public function title(): string
    {
        return 'Case Details';
    }

    // Set Column/Row Styling after sheet has been draw
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:A8')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);
            },
        ];
    } 
}
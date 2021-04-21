<?php

namespace App\Excel\Sheets;

use App\Defect;
use App\Constants\DefectStatus;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;


class DefectsSummarySheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithEvents
{
    protected $case_id;

    public function __construct($case_id)
    {
       $this->case_id = $case_id;
    }

    public function collection()
    {
        $defects = Defect::with(['case', 'assigned_contractor', 'type', 'tags'])->where('case_id', $this->case_id)->get();
        return $defects;
    }

    // Select data from query and set its position
    public function map($defect): array
    {
        $tagEntries = $defect->tags->toArray();
        $tagsString = "";
        for($i = 0; $i < count($tagEntries); $i++) {
            if($i != 0) {
                $tagsString .= ", ";
            }
            if(empty($tagEntries[$i])) {
                dd($i);
            }
            $tagsString .= $tagEntries[$i]["tag"];
        }

        return [
            "C".$defect->case->ref_no."-D".$defect->ref_no,
            $defect->title,
            DefectStatus::$dict[$defect->status],
            $defect->type['title'],
            $defect->extended_count,
            $tagsString,
        ];
    }

    // Add heading for columns
    public function headings(): array
    {
        return [
            'Defect No.',
            'Title',
            'Status',
            'Defect Type',
            'Extended Count',
            'Tag',
        ];
    }

    // Set the title of the sheet
    public function title(): string
    {
        return 'Defects Summary';
    }

    // Set Column/Row Styling after sheet has been draw
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
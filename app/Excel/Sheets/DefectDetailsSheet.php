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
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;


class DefectDetailsSheet implements FromCollection, ShouldAutoSize, WithTitle, WithEvents
{
    protected $id;

    public function __construct($id)
    {
       $this->id = $id;
    }

    public function collection()
    {
        $defect = Defect::with(['case', 'assigned_contractor', 'type', 'tags'])
                           ->where('id', $this->id)->first();

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

        return collect([
            [
                'title' => 'Case No.',
                'detail' => "C".$defect->case->ref_no,
            ],
            [
                'title' => 'Case Title',
                'detail' => $defect->case->title,
            ],
            [
                'title' => 'Defect No.',
                'detail' => "C".$defect->case->ref_no."-D".$defect->ref_no,
            ],
            [
                'title' => 'Title',
                'detail' => $defect->title,  
            ],
            [
                'title' => 'Status',
                'detail' => defectstatus::$dict[$defect->status], 
            ],
            [
                'title' => 'Defect Type',
                'detail' => $defect->type['title'],
            ],
            [
                'title' => 'Extended Count',
                'detail' => $defect->extended_count,
            ],
            [
                'title' => 'Tag(s)',
                'detail' => $tagsString,
            ],
        ]);
    }

    // Set the title of the sheet
    public function title(): string
    {
        return 'Defect Details';
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
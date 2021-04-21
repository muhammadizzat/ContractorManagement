<?php

namespace App\Excel\Sheets;

use App\Developer;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;


class DeveloperDetailsSheet implements FromCollection, ShouldAutoSize, WithTitle, WithEvents
{
    protected $dev_id;

    public function __construct($dev_id)
    {
       $this->dev_id = $dev_id;
    }

    public function collection()
    {
        $developer = Developer::where('id', $this->dev_id)->first();
        
        // Create custom data array
        return collect([
            [
                'title' => 'Developer Name',
                'detail' => $developer->name,
                
            ]
        ]);
    }

    // Set the title of the sheet
    public function title(): string
    {
        return 'Developer Details';
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

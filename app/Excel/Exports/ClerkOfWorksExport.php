<?php

namespace App\Excel\Exports;

use App\ClerkOfWork;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class ClerkOfWorksExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithEvents, WithColumnFormatting
{   
    protected $dev_id;

    public function __construct($dev_id)
    {
       $this->dev_id = $dev_id;
    }
    
    public function collection()
    {
        return ClerkOfWork::with(['user', 'developer'])->where('developer_id', $this->dev_id)->get();
    }

    public function title(): string
    {
        return 'Clerk of Works';
    }

    // Select data from query and set its position
    public function map($clerk_of_work): array
    {
        return [
            $clerk_of_work->user->name,
            $clerk_of_work->developer->name,
            Date::dateTimeToExcel($clerk_of_work->created_at),
        ];
    }
    
    public function columnFormats(): array
    {
        return [
            'C' => 'dd mmm yyyy',
        ];
    }

    public function headings(): array
    {
        return [
            'Name',
            'Developer Name',
            'Created At',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:C1')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);
            },
        ];
    }
}
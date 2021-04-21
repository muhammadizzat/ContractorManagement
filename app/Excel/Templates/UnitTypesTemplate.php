<?php

namespace App\Excel\Templates;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class UnitTypesTemplate implements FromCollection, ShouldAutoSize, WithTitle, WithEvents
{
    public function collection()
    {
        return collect([
            [
                'column_name' => 'Unit Types',
            ]
        ]);
    }

    public function title(): string
    {
        return 'Unit Types Import';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);
            },
        ];
    }
}
<?php

namespace App\Excel\Templates;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class UnitsTemplate implements FromCollection, ShouldAutoSize, WithTitle, WithEvents
{
    public function collection()
    {
        return collect([
                [
                    'unit_numbers' => 'Unit Numbers',
                    'unit_types' => 'Unit Types',
                    'owner_names' => 'Owner Names',
                    'owner_contact_numbers' => 'Owner Contact Numbers',
                    'owner_email_addresses' => 'Owner Email Addresses',
                ]
        ]);
    }

    public function title(): string
    {
        return 'Units Import';
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
<?php

namespace App\Excel\Exports;

use App\DeveloperContractorAssociation;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use App\Excel\Sheets\DeveloperDetailsSheet;

class ContractorAssociationsExport implements WithMultipleSheets
{
    protected $dev_id;
    
    // Get arguement when pass from function called
    public function __construct(int $dev_id)
    {
        $this->dev_id = $dev_id;
    }

    // Create multiple sheet
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new ContractorAssociationsSheet($this->dev_id);
        $sheets[] = new DeveloperDetailsSheet($this->dev_id);
        
        return $sheets;
    }
}

class ContractorAssociationsSheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithEvents, WithColumnFormatting
{
    public function __construct($dev_id)
    {
       $this->dev_id = $dev_id;
    }

    public function collection()
    {
        return DeveloperContractorAssociation::with('user.contractor')->where('developer_id', $this->dev_id)->get();
    }

    public function title(): string
    {
        return 'Contractor Scope of Work';
    }

    // Select data from query and set its position
    public function map($contractor_association): array
    {
        return [
            $contractor_association->user->name,
            $contractor_association->user->email,
            $contractor_association->user->contractor->contact_no,
            Date::dateTimeToExcel($contractor_association->created_at),
        ];
    }
    // Set Date Format
    public function columnFormats(): array
    {
        return [
            'D' => 'dd mmm yyyy',
        ];
    }

    // Add heading for columns
    public function headings(): array
    {
        return [
            'Contractor',
            'Email',
            'Contact No.',
            'Associated Date',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:D1')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);
            },
        ];
    }
}

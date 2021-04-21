<?php

namespace App\Excel\Exports;

use App\User;
use App\DeveloperAdmin;

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

class AdminsExport implements WithMultipleSheets
{
    protected $role;
    protected $dev_id;

    public function __construct($role, $dev_id)
    {
       $this->role = $role;
       $this->dev_id = $dev_id;
    }

    // Create multiple sheet
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new AdminsSheet($this->role, $this->dev_id);
        
        if($this->role == 'dev-admin' && $this->dev_id != null){
            $sheets[] = new DeveloperDetailsSheet($this->dev_id);
        }  
          
        return $sheets;
    }
}

class AdminsSheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithEvents, WithColumnFormatting
{

    protected $role;
    protected $dev_id;

    public function __construct($role, $dev_id)
    {
       $this->role = $role;
       $this->dev_id = $dev_id;
    }

    public function collection()
    {
        if($this->role == 'admin'){
            return User::whereHas('roles', function ($query) {$query->where('name', 'admin');})->get();
        } else if($this->role == 'dev-admin'){
            if($this->dev_id != null){
                return DeveloperAdmin::with(['user', 'developer'])->where('developer_id', $this->dev_id)->get();
            } else {
                return DeveloperAdmin::with(['user', 'developer'])->get();
            }
            
        }
    }

    public function title(): string
    {
        if($this->role == 'admin'){
            return 'LinkZZapp Admins';
        } else if($this->role == 'dev-admin'){
            return 'Developer Admins';
        } 
    }

    // Select data from query and set its position
    public function map($admin): array
    {
        if($this->role == 'admin'){
            return [
                $admin->name,
                $admin->email,
                Date::dateTimeToExcel($admin->created_at),
            ];
        } else if($this->role == 'dev-admin') {
            return [
                $admin->user->name,
                $admin->user->email,
                Date::dateTimeToExcel($admin->user->created_at),
            ];
        }
    }
    // Set Date Format
    public function columnFormats(): array
    {
        return [
            'C' => 'dd mmm yyyy',
        ];
    }

    // Add heading for columns
    public function headings(): array
    {
        return [
            'Name',
            'Email',
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
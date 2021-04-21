<?php

namespace App\Excel\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use App\Excel\Sheets\DefectDetailsSheet;

class DefectReportExport implements WithMultipleSheets
{
    protected $id;
    
    // Get arguement when pass from function called
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    // Create multiple sheet
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new DefectDetailsSheet($this->id);

        return $sheets;
    }
}
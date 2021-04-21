<?php

namespace App\Excel\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use App\Excel\Sheets\CaseDetailsSheet;
use App\Excel\Sheets\DefectsSummarySheet;

class CaseReportExport implements WithMultipleSheets
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

        $sheets[] = new CaseDetailsSheet($this->id);
        $sheets[] = new DefectsSummarySheet($this->id);

        return $sheets;
    }
}
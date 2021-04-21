<?php

namespace App\Excel\Imports;

use App\Unit;
use App\UnitType;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UnitsImport implements ToCollection, WithHeadingRow
{
    protected $user_id;
    protected $proj_id;
    
    public function __construct($user_id, $proj_id)
    {
        $this->user_id = $user_id;
        $this->proj_id = $proj_id;
    }
 
    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {   
            $unit_type = UnitType::where('name', $row['unit_types'])->where('project_id', $this->proj_id)->first();
            Unit::create([
                'unit_no' => $row['unit_numbers'],
                'unit_type_id' => $unit_type['id'],
                'owner_name' => $row['owner_names'],
                'owner_contact_no' => $row['owner_contact_numbers'],
                'owner_email' => $row['owner_email_addresses'],
                'created_by' => $this->user_id,
                'project_id' => $this->proj_id,
            ]);
            
        } 
    }
}
<?php

namespace App\Excel\Imports;

use App\UnitType;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UnitTypesImport implements ToCollection, WithHeadingRow
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
            $unit_types_count = UnitType::where('name', $row['unit_types'])->where('project_id', $this->proj_id)->count();
            if ($unit_types_count == 0){
                UnitType::create([
                    'name'       => $row['unit_types'],
                    'created_by' => $this->user_id,
                    'project_id' => $this->proj_id,
                ]);
            } 
        } 
    }
}
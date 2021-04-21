<?php

namespace App\Http\Resources\Developer;

use Illuminate\Http\Resources\Json\JsonResource;

class UnitInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'unit_no' => $this->unit_no,
            'owner_name' => $this->owner_name,
            'unit_type_id' => $this->unit_type_id
        ];
    }
}

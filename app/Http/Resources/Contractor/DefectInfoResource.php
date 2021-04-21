<?php

namespace App\Http\Resources\Contractor;

use Illuminate\Http\Resources\Json\JsonResource;

use App\ProjectCase;

class DefectInfoResource extends JsonResource
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
            'case_ref_no' => $this->whenLoaded('case')->ref_no,
            'ref_no' => $this->ref_no,
            'title' => $this->title,
            'status' => $this->status,
            'case_unit_id' => $this->whenLoaded('case')->unit_id,
            'type' => new DefectTypeInfoResource($this->whenLoaded('type')),
            'assigned_contractor' => new UserResource($this->whenLoaded('assigned_contractor')),
            'created_at' => $this->created_at,
            'due_date' => $this->due_date,
        ];
    }
}

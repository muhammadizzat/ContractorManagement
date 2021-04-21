<?php

namespace App\Http\Resources\Contractor;

use Illuminate\Http\Resources\Json\JsonResource;


class DefectResource extends JsonResource
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
            'description' => $this->description,
            'status' => $this->status,
            'case_unit_id' => $this->whenLoaded('case')->unit_id,
            'type' => new DefectTypeInfoResource($this->whenLoaded('type')),
            'due_date' => $this->due_date,
            'assigned_contractor' => new UserResource($this->whenLoaded('assigned_contractor')),
            'unit_type_floor_id' => $this->unit_type_floor_id,

            'tags' => DefectTagResource::collection($this->whenLoaded('tags')),
            'pins' => DefectPinResource::collection($this->whenLoaded('pins')),
            'images' => DefectImageResource::collection($this->whenLoaded('images')),

            'created_at' =>$this->created_at,
            'closed_date' => $this->closed_date,
            'resolved_date' => $this->resolved_date
        ];
    }
}

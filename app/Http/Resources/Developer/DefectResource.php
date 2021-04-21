<?php

namespace App\Http\Resources\Developer;

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
        // dd($this->whenLoaded('case'));
        return [
            'id' => $this->id,
            'ref_no' => $this->ref_no,
            'case_id' => $this->case_id,
            'case_ref_no' => $this->whenLoaded('case', function () { return $this->case->ref_no; }),
            'title' => $this->title,
            'status' => $this->status,
            'closed_status' => $this->closed_status,
            'duplicate_defect_id' => $this->duplicate_defect_id,
            'reject_reason' => $this->reject_reason,
            'description' => $this->description,
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

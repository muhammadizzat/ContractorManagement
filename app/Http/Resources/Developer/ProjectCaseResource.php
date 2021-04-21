<?php

namespace App\Http\Resources\Developer;

use Illuminate\Http\Resources\Json\JsonResource;


class ProjectCaseResource extends JsonResource
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
            'ref_no' => $this->ref_no,
            'title' => $this->title,
            'status' => $this->status,
            'assigned_cow' => new UserResource($this->whenLoaded('assigned_cow')),
            'defects' => DefectInfoResource::collection($this->whenLoaded('defects')),
            'unit' => new UnitInfoResource($this->whenLoaded('unit')),
            'description' => $this->description,
            'tags' => CaseTagResource::collection($this->whenLoaded('tags')),
            'created_at' => $this->created_at,
        ];
    }
}

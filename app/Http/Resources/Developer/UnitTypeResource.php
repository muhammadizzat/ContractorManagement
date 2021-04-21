<?php

namespace App\Http\Resources\Developer;

use Illuminate\Http\Resources\Json\JsonResource;

class UnitTypeResource extends JsonResource
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
            'name' => $this->name,
            'floors' => UnitTypeFloorResource::collection($this->whenLoaded('floors')),
        ];
    }
}

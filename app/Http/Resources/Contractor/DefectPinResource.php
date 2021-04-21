<?php

namespace App\Http\Resources\Contractor;

use Illuminate\Http\Resources\Json\JsonResource;


class DefectPinResource extends JsonResource
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
            'label' => $this->label,
            'x' => $this->x,
            'y' => $this->y,
        ];
    }
}

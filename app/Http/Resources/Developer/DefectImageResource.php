<?php

namespace App\Http\Resources\Developer;

use Illuminate\Http\Resources\Json\JsonResource;


class DefectImageResource extends JsonResource
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
        ];
    }
}

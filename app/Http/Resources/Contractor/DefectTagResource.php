<?php

namespace App\Http\Resources\Contractor;

use Illuminate\Http\Resources\Json\JsonResource;

use App\ProjectCase;

class DefectTagResource extends JsonResource
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
            'tag' => $this->tag,
        ];
    }
}

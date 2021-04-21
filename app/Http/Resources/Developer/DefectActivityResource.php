<?php

namespace App\Http\Resources\Developer;

use Illuminate\Http\Resources\Json\JsonResource;


class DefectActivityResource extends JsonResource
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
            'type' => $this->type,
            'content' => $this->content,
            'created_at' => $this->created_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'images' => DefectActivityImageResource::collection($this->whenLoaded('images')),
            'request_type' => $this->request_type,
            'request_response_user' => new UserResource($this->whenLoaded('request_response_user')),
            'request_response' => $this->request_response,
        ];
    }
}

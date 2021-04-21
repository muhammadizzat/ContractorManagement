<?php

namespace App\Http\Resources\Developer;

use Illuminate\Http\Resources\Json\JsonResource;

use App\ProjectCase;

class ProjectResource extends JsonResource
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
            'status' => $this->status,
            'cases_stats' => [
                'open_qty' => ProjectCase::open()->where('project_id', $this->id)->count(),
                'overdue_qty' => ProjectCase::withOverdueDefects()->where('project_id', $this->id)->count(),
            ]
        ];
    }
}

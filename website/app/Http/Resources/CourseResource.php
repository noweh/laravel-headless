<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'published' => $this->published,
            'format' => $this->format,
            'module_id' => $this->module_id,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
            'active' => $this->active,
            'title' => $this->title,
            'description' => $this->description,
            'themes' => $this->when($this->relationLoaded('themes'), function () {
                return ThemeResource::collection($this->themes);
            }),
            'position' => $this->position
        ];
    }
}

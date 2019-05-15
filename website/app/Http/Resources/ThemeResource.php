<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ThemeResource extends JsonResource
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
            'code' => $this->code,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
            'active' => $this->active,
            'label' => $this->label,
            'courses' => $this->when($this->relationLoaded('courses'), function () {
                return CourseResource::collection($this->courses);
            }),
            'sessions' => $this->when($this->relationLoaded('sessions'), function () {
                return SessionResource::collection($this->sessions);
            }),
            'questionnaires' => $this->when($this->relationLoaded('questionnaires'), function () {
                return QuestionnaireResource::collection($this->questionnaires);
            }),
        ];
    }
}

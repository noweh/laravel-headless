<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SessionResource extends JsonResource
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
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
            'active' => $this->active,
            'title' => $this->title,
            'description' => $this->description,
            'themes' => $this->when($this->relationLoaded('themes'), function () {
                return ThemeResource::collection($this->themes);
            }),
            'courses' => $this->when($this->relationLoaded('courses'), function () {
                $this->courses->each(function ($course) {
                    $course->position_in_session = $course->pivot->position_in_session;
                });
                return CourseResource::collection($this->courses);
            }),
            'questionnaires' => $this->when($this->relationLoaded('questionnaires'), function () {
                $this->questionnaires->each(function ($questionnaire) {
                    $questionnaire->position_in_session = $questionnaire->pivot->position_in_session;
                });
                return QuestionnaireResource::collection($this->questionnaires);
            }),
            'position' => $this->position
        ];
    }
}

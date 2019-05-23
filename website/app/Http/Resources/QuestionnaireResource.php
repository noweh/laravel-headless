<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionnaireResource extends JsonResource
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
            'level' => $this->level,
            'note_max' => $this->note_max,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
            'active' => $this->active,
            'title' => $this->title,
            'description' => $this->description,
            'themes' => $this->when($this->relationLoaded('themes'), function () {
                return ThemeResource::collection($this->themes);
            }),
            'sessions' => $this->when($this->relationLoaded('sessions'), function () {
                return SessionResource::collection($this->sessions);
            }),
            'questions' => $this->when($this->relationLoaded('questions'), function () {
                $this->questions->each(function ($question) {
                    $question->position = $question->pivot->position;
                });
                return QuestionResource::collection($this->questions);
            }),
            'position' => $this->when($this->position_in_session, function () {
                return $this->position_in_session;
            })
        ];
    }
}

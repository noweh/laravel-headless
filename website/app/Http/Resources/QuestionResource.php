<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
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
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
            'duration_min' => $this->duration_min,
            'duration_max' => $this->duration_max,
            'active' => $this->active,
            'title' => $this->title,
            'description' => $this->description,
            'possibleAnswers' => PossibleAnswerResource::collection($this->possibleAnswers),
            'goodAnswer' => PossibleAnswerResource::make($this->goodAnswer),
            'position' => $this->position
        ];
    }
}

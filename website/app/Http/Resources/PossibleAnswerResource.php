<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PossibleAnswerResource extends JsonResource
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
            'question_id' => $this->question_id,
            'format' => $this->format,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
            'active' => $this->active,
            'text' => $this->text,
            'description' => $this->description,
            'position' => $this->position
        ];
    }
}

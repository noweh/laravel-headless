<?php

namespace App\Models;

class UserQuestionnaire extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'note'
    ];

    public function questionnaires()
    {
        return $this->belongsToMany('App\Models\Questionnaire');
    }
}

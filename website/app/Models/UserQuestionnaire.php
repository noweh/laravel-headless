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
        'user_id',
        'questionnaire_id',
        'note'
    ];

    public function questionnaires()
    {
        return $this->belongsToMany('App\Models\Questionnaire');
    }
}

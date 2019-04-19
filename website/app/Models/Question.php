<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;

class Question extends BaseModel
{
    use Translatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'published',
        'duration_min',
        'duration_max',
        'position'
    ];

    /**
     * The attributes that are translated.
     *
     * @var array
     */
    public $translatedAttributes = [
        'active',
        'title',
        'description'
    ];

    public function questionnaire()
    {
        return $this->belongsTo('App\Models\Questionnaire');
    }

    public function type()
    {
        return $this->belongsTo('App\Models\QuestionType');
    }

    public function possibleAnswers()
    {
        return $this->hasMany('App\Models\PossibleAnswer')->orderBy('position', 'asc');
    }
}

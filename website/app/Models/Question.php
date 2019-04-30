<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;

class Question extends AbstractModel
{
    use Translatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'published',
        'format',
        'duration_min',
        'duration_max',
        'position',
        'questionnaire_id',
        'question_type_id',
        'good_answer_id'
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
        return $this->belongsTo(Questionnaire::class, 'questionnaire_id');
    }

    public function type()
    {
        return $this->belongsTo(QuestionType::class, 'question_type_id');
    }

    public function possibleAnswers()
    {
        return $this->hasMany(PossibleAnswer::class)->orderBy('position', 'asc');
    }

    public function availablePossibleAnswers()
    {
        return $this->hasMany(PossibleAnswer::class)->orderBy('position', 'asc')
            ->published()->withActiveTranslations();
    }

    public function goodAnswer()
    {
        return $this->hasOne(PossibleAnswer::class, 'id', 'good_answer_id');
    }
}

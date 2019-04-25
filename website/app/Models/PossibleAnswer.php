<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;

class PossibleAnswer extends BaseModel
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
        'position',
        'question_id'
    ];

    /**
     * The attributes that are translated.
     *
     * @var array
     */
    public $translatedAttributes = [
        'active',
        'text',
        'description'
    ];

    public function question()
    {
        return $this->belongsTo('App\Models\Question', 'question_id');
    }
}

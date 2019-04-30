<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;

class QuestionType extends AbstractModel
{
    use Translatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'published',
        'code'
    ];

    /**
     * The attributes that are translated.
     *
     * @var array
     */
    public $translatedAttributes = [
        'active',
        'label'
    ];

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('position', 'asc');
    }
}

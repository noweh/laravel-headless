<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;

class Questionnaire extends BaseModel
{
    use Translatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'published',
        'level',
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

    public function themes()
    {
        return $this->belongsToMany('App\Models\Themes');
    }

    public function questions()
    {
        return $this->hasMany('App\Models\Question')->orderBy('position', 'asc');
    }
}

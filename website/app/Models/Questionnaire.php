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
        'note_max',
        'position',
        'module_id'
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

    public function module()
    {
        return $this->belongsTo('App\Models\Module', 'module_id');
    }

    public function themes()
    {
        return $this->belongsToMany('App\Models\Theme');
    }

    public function availableThemes()
    {
        return $this->belongsToMany('App\Models\Theme')->published()->withActiveTranslations();
    }

    public function questions()
    {
        return $this->hasMany('App\Models\Question')->orderBy('position', 'asc');
    }

    public function availableQuestions()
    {
        return $this->hasMany('App\Models\Question')->orderBy('position', 'asc')->published()->withActiveTranslations();
    }
}

<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;

class Module extends AbstractModel
{
    use Translatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'published',
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

    public function courses()
    {
        return $this->hasMany('App\Models\Course')->orderBy('position', 'asc');
    }

    public function availableCourses()
    {
        return $this->hasMany('App\Models\Course')->orderBy('position', 'asc')
            ->published()->withActiveTranslations();
    }

    public function questionnaires()
    {
        return $this->hasMany('App\Models\Questionnaire')->orderBy('position', 'asc');
    }

    public function availableQuestionnaires()
    {
        return $this->hasMany('App\Models\Questionnaire')->orderBy('position', 'asc')
            ->published()->withActiveTranslations();
    }

    public function themes()
    {
        return $this->belongsToMany('App\Models\Theme');
    }

    public function availableThemes()
    {
        return $this->belongsToMany('App\Models\Theme')->published()->withActiveTranslations();
    }
}

<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;

class Theme extends AbstractModel
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
    protected $translatedAttributes = [
        'active',
        'label'
    ];

    public function modules()
    {
        return $this->belongsToMany(Module::class)->orderBy('position', 'asc');
    }

    public function availableModules()
    {
        return $this->belongsToMany(Module::class)->orderBy('position', 'asc')
            ->published()->withActiveTranslations();
    }

    public function questionnaires()
    {
        return $this->belongsToMany(Questionnaire::class)->orderBy('position', 'asc');
    }

    public function availableQuestionnaires()
    {
        return $this->belongsToMany(Questionnaire::class)->orderBy('position', 'asc')
            ->published()->withActiveTranslations();
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class)->orderBy('position', 'asc');
    }

    public function availableCourses()
    {
        return $this->belongsToMany(Course::class)->orderBy('position', 'asc')
            ->published()->withActiveTranslations();
    }
}

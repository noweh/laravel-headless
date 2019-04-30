<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;

class Questionnaire extends AbstractModel
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
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function themes()
    {
        return $this->belongsToMany(Theme::class);
    }

    public function availableThemes()
    {
        return $this->belongsToMany(Theme::class)->published()->withActiveTranslations();
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('position', 'asc');
    }

    public function availableQuestions()
    {
        return $this->hasMany(Question::class)->orderBy('position', 'asc')->published()->withActiveTranslations();
    }
}

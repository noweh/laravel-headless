<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;

class Course extends BaseModel
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

    public function module()
    {
        return $this->belongsTo('App\Models\Module');
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

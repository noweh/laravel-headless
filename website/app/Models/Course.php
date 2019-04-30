<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;

class Course extends AbstractModel
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
}

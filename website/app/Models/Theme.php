<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;

class Theme extends BaseModel
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

    public function modules()
    {
        return $this->hasMany('App\Models\Module')->orderBy('position', 'asc');
    }

    public function questionnaires()
    {
        return $this->hasMany('App\Models\Questionnaire')->orderBy('position', 'asc');
    }
}

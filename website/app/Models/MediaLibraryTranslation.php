<?php

namespace App\Models;

class MediaLibraryTranslation extends AbstractModel
{
    public $timestamps = false;
    public $pivotAttributes = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'legend'
    ];
}

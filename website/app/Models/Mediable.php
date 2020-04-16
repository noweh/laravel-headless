<?php

namespace App\Models;

class Mediable extends AbstractModel
{
    public $pivotAttributes = [];

    protected $fillable = [
        'crop_x',
        'crop_y',
        'crop_h',
        'crop_w',
        'media_library_id',
        'ratio',
        'mediable_id',
        'mediable_type',
        'position'
    ];
}

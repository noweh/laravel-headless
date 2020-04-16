<?php

namespace App\Models;

class MediaLibrarySlug extends AbstractModel
{
    public $pivotAttributes = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'slug'
    ];

    public function slugs()
    {
        return $this->hasOne(MediaLibrary::class);
    }
}

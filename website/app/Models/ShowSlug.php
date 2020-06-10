<?php

namespace App\Models;

class ShowSlug extends AbstractModel
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'show_slug_id';

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
        return $this->hasOne(Show::class, 'show_id', 'show_id');
    }
}

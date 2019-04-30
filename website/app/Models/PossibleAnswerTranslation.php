<?php namespace App\Models;

class PossibleAnswerTranslation extends AbstractModel
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'text',
        'description'
    ];
}

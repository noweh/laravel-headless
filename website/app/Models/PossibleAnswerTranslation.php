<?php namespace App\Models;

class PossibleAnswerTranslation extends BaseModel
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

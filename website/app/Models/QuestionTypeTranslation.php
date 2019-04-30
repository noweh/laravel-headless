<?php namespace App\Models;

class QuestionTypeTranslation extends AbstractModel
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'label'
    ];
}

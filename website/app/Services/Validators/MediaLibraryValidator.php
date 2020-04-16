<?php

namespace App\Services\Validators;

class MediaLibraryValidator extends AbstractValidator
{
    public $rules = [
        'internal_title' => 'required',
        'url' => 'required',
        'width' => 'required',
        'height' => 'required',
        'public_id' => 'required',
        'format' => 'required'
    ];
    public $translatedFieldsRules = [];
}

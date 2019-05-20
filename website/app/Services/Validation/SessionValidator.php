<?php

namespace App\Services\Validation;

class SessionValidator extends AbstractValidator
{
    public $rules = [];
    public $translatedFieldsRules = ['title' => 'required|min:3'];
}

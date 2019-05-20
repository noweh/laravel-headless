<?php

namespace App\Services\Validation;

class ThemeValidator extends AbstractValidator
{
    public $rules = ['code' => 'required'];
    public $translatedFieldsRules = ['label' => 'required|min:3'];
}

<?php

namespace App\Services\Validation;

class PossibleAnswerValidator extends AbstractValidator
{
    public $rules = ['format' => 'required'];
    public $translatedFieldsRules = ['text' => 'required|min:3'];
}

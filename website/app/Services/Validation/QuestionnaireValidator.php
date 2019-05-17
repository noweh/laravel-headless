<?php

namespace App\Services\Validation;

class QuestionnaireValidator extends AbstractValidator
{
    public $rules = ['published' => 'required|boolean'];
    public $translatedFieldsRules = ['title' => 'required|min:3'];
}

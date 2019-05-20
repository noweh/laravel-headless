<?php

namespace App\Services\Validation;

class QuestionValidator extends AbstractValidator
{
    public $rules = ['format' => 'required'];
    public $translatedFieldsRules = ['title' => 'required|min:3'];
}

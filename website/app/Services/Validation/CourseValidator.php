<?php

namespace App\Services\Validation;

class CourseValidator extends AbstractValidator
{
    public $rules = [];
    public $translatedFieldsRules = ['title' => 'required|min:3'];
}

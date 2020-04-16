<?php

namespace App\Services\Validators;

class AdminUserValidator extends AbstractValidator
{
    public $rules = [
        'name' => 'required|string|min:3|max:255',
        'email' => 'required|string|email|max:255|unique:admin_users',
        'password' => 'required|string|min:6|confirmed'
    ];
}

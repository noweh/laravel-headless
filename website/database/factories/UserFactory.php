<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\AdminUser;
use Faker\Generator as Faker;

$factory->define(AdminUser::class, function (Faker $faker) {
    $email = $faker->unique()->safeEmail;
    return [
        'name' => $faker->name,
        'email' => $email,
        'password' => Hash::make(explode('@', $email)[0]), // password is mbox part of email
    ];
});

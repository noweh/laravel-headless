<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Course;
use Faker\Generator as Faker;
use Faker\Factory as FakerFactory;

$fakerFr = FakerFactory::create('fr_FR');

$factory->define(Course::class, function (Faker $faker) use ($fakerFr) {
    return [
        'published' => $faker->boolean(50),
        'format' => 'text',
        'active:fr' => $faker->boolean(100),
        'title:fr' => $fakerFr->sentence(5),
        'description:fr' => $fakerFr->realText(rand(80, 600)),
        'active:en' => $faker->boolean(50),
        'title:en' => $faker->sentence(5),
        'description:en' => $faker->realText(rand(80, 600))
    ];
});

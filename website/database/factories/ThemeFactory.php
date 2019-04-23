<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Theme;
use Faker\Generator as Faker;
use Faker\Factory as FakerFactory;

$fakerFr = FakerFactory::create('fr_FR');

$factory->define(Theme::class, function (Faker $faker) use ($fakerFr) {
    return [
        'published' => $faker->boolean(75),
        'code' => $faker->word,
        'active:fr' => $faker->boolean(100),
        'label:fr' => $fakerFr->word,
        'active:en' => $faker->boolean(50),
        'label:en' => $faker->word
    ];
});

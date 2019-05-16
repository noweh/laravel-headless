<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Question;
use App\Models\QuestionType;
use Faker\Generator as Faker;
use Faker\Factory as FakerFactory;

$fakerFr = FakerFactory::create('fr_FR');

$factory->define(Question::class, function (Faker $faker) use ($fakerFr) {
    return [
        'published' => $faker->boolean(50),
        'format' => 'text',
        'duration_min' => $faker->numberBetween(1, 50),
        'duration_max' => $faker->numberBetween(50, 100),
        'active:fr' => $faker->boolean(100),
        'title:fr' => $fakerFr->sentence(5),
        'description:fr' => $fakerFr->realText(rand(80, 600)),
        'active:en' => $faker->boolean(50),
        'title:en' => $faker->sentence(5),
        'description:en' => $faker->realText(rand(80, 600)),
        'question_type_id' => function () {
            // Get random question type id
            return QuestionType::inRandomOrder()->first()->id;
        }
    ];
});

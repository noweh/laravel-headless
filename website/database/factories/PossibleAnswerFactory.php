<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\PossibleAnswer;
use App\Models\Question;
use Faker\Generator as Faker;
use Faker\Factory as FakerFactory;

$fakerFr = FakerFactory::create('fr_FR');
$autoIncrement = autoIncrement();
$autoIncrement->rewind();

$factory->define(PossibleAnswer::class, function (Faker $faker) use ($fakerFr, $autoIncrement) {
    $autoIncrement->next();

    return [
        'published' => $faker->boolean(50),
        'format' => 'text',
        'position' => $autoIncrement->current(),
        'active:fr' => $faker->boolean(100),
        'text:fr' => $faker->realText(rand(20, 80)),
        'description:fr' => $fakerFr->realText(rand(80, 600)),
        'active:en' => $faker->boolean(50),
        'text:en' => $faker->realText(rand(20, 80)),
        'description' => $faker->realText(rand(80, 600)),
        'question_id' => function () {
            // Get random question id
            return Question::inRandomOrder()->first()->id;
        },
    ];
});

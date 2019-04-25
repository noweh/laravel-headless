<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\QuestionType;
use Faker\Generator as Faker;
use Faker\Factory as FakerFactory;

$fakerFr = FakerFactory::create('fr_FR');
$autoIncrement = autoIncrement();
$autoIncrement->rewind();

$factory->define(Question::class, function (Faker $faker) use ($fakerFr, $autoIncrement) {
    $autoIncrement->next();

    return [
        'published' => $faker->boolean(50),
        'format' => 'text',
        'duration_min' => $faker->numberBetween(1, 50),
        'duration_max' => $faker->numberBetween(50, 100),
        'position' => $autoIncrement->current(),
        'active:fr' => $faker->boolean(100),
        'title:fr' => $fakerFr->sentence(5),
        'description:fr' => $fakerFr->realText(rand(80, 600)),
        'active:en' => $faker->boolean(50),
        'title:en' => $faker->sentence(5),
        'description:en' => $faker->realText(rand(80, 600)),
        'questionnaire_id' => function () {
            // Get random questionnaire id
            return Questionnaire::inRandomOrder()->first()->id;
        },
        'question_type_id' => function () {
            // Get random question type id
            return QuestionType::inRandomOrder()->first()->id;
        }
    ];
});

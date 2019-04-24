<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Questionnaire;
use App\Models\UserQuestionnaire;
use Faker\Generator as Faker;
use Faker\Factory as FakerFactory;

$fakerFr = FakerFactory::create('fr_FR');

$factory->define(UserQuestionnaire::class, function (Faker $faker) use ($fakerFr) {
    return [
        'user_id' => 1,
        'questionnaire_id' => function () {
            // Get random questionnaire id
            return Questionnaire::inRandomOrder()->first()->id;
        },
        'note' => $faker->numberBetween(0, 20)
    ];
});

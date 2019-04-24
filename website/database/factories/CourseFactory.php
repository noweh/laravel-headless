<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Course;
use App\Models\Module;
use Faker\Generator as Faker;
use Faker\Factory as FakerFactory;

$fakerFr = FakerFactory::create('fr_FR');
$autoIncrement = autoIncrement();
$autoIncrement->rewind();

$factory->define(Course::class, function (Faker $faker) use ($fakerFr, $autoIncrement) {
    $autoIncrement->next();

    return [
        'published' => $faker->boolean(50),
        'format' => 'text',
        'position' => $autoIncrement->current(),
        'active:fr' => $faker->boolean(100),
        'title:fr' => $fakerFr->sentence(5),
        'description:fr' => $fakerFr->realText(rand(80, 600)),
        'active:en' => $faker->boolean(50),
        'title:en' => $faker->sentence(5),
        'description' => $faker->realText(rand(80, 600)),
        'module_id' => function () {
            // Get random module id
            return Module::inRandomOrder()->first()->id;
        }
    ];
});

function autoIncrement()
{
    for ($i = 0; $i < 1000; ++$i) {
        yield $i;
    }
}

<?php

use Illuminate\Database\Seeder;
use App\Models\Questionnaire;
use App\Models\Theme;

class QuestionnaireTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Creating 10 questionnaires.");

        // Create the Questionnaire
        factory(Questionnaire::class, 10)->create();

        // Get all the themes attaching up to 10 random themes to each module
        $themes = Theme::all();

        // Populate the pivot table
        Questionnaire::all()->each(function ($questionnaire) use ($themes) {
            /** @var Questionnaire $questionnaire */
            $questionnaire->themes()->attach(
                $themes->random(rand(1, 3))->pluck('id')->toArray()
            );
        });

        $this->command->info('Questionnaires Created!');
    }
}

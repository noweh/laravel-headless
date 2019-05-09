<?php

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Questionnaire;

class QuestionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Creating 50 questions.");

        // Create the Question
        factory(Question::class, 50)->create();

        // Get all the themes attaching up to 10 random themes to each module
        $questionnaires = Questionnaire::all();

        // Populate the pivot table
        Question::all()->each(function ($question) use ($questionnaires) {
            /** @var Questionnaire $questionnaire */
            $question->questionnaires()->attach(
                $questionnaires->random(rand(1, 3))->pluck('id')->toArray(),
                ['position' => 1]
            );
        });

        $this->command->info('Questions Created!');
    }
}

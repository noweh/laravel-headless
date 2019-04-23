<?php

use Illuminate\Database\Seeder;
use App\Models\PossibleAnswer;
use App\Models\Question;

class PossibleAnswerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Creating 250 possible answers.");

        // Create the Question
        factory(PossibleAnswer::class, 250)->create();

        $this->command->info('Possible Answers Created!');

        $this->command->info("Set good answer for questions.");

        // Set good answer to each question
        Question::all()->each(function ($question) {
            /** @var Question $question */
            if ($question->availablePossibleAnswers()->get()->count() > 0) {
                $question->update([
                    'good_answer_id' => $question->availablePossibleAnswers()->get()->random()->id
                ]);
            }
        });

        $this->command->info('Good Answers Setted!');
    }
}

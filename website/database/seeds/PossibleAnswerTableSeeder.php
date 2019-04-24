<?php

use Illuminate\Database\Seeder;
use App\Models\PossibleAnswer;
use App\Models\Question;
use App\Models\QuestionType;

class PossibleAnswerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Creating 250 possible answers for QCM.");

        // Create the Question for qcm
        factory(PossibleAnswer::class, 250)->create();

        $this->command->info("Creating 250 possible answers for yes/no questions.");

        // Create the Question for yes/no
        $question_type_id = QuestionType::where('code', 'vrai/faux')->first()->id;
        Question::where('question_type_id', $question_type_id)->each(function ($question) {
            PossibleAnswer::create([
                'published' => true,
                'format' => 'text',
                'position' => 1,
                'active:fr' => true,
                'text:fr' => 'Oui',
                'description:fr' => null,
                'active:en' => true,
                'text:en' => 'Yes',
                'description' => null,
                'question_id' => $question->id
            ]);

            PossibleAnswer::create([
                'published' => true,
                'format' => 'text',
                'position' => 2,
                'active:fr' => true,
                'text:fr' => 'Non',
                'description:fr' => null,
                'active:en' => true,
                'text:en' => 'No',
                'description' => null,
                'question_id' => $question->id
            ]);
        });

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

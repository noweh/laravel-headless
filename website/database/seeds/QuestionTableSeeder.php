<?php

use Illuminate\Database\Seeder;
use App\Models\Question;

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

        $this->command->info('Questions Created!');
    }
}

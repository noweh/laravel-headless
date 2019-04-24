<?php

use Illuminate\Database\Seeder;
use App\Models\UserQuestionnaire;

class UserQuestionnaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Creating 5 user questionnaires.");

        // Create the User Questionnaire
        factory(UserQuestionnaire::class, 5)->create();

        $this->command->info('User Questionnaires Created!');
    }
}

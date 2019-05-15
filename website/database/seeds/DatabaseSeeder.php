<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();

        // Ask for db migration refresh, default is no
        if ($this->command->confirm('Do you wish to refresh migration before seeding, it will clear all old data ?')) {
            // Call the php artisan migrate:fresh using Artisan
            $this->command->call('migrate:fresh');

            $this->command->line("Database cleared.");
        }

        // $this->call(UsersTableSeeder::class);
        $this->call(ThemeTableSeeder::class);
        $this->call(SessionTableSeeder::class);
        $this->call(CourseTableSeeder::class);
        $this->call(QuestionnaireTableSeeder::class);
        $this->call(QuestionTypeTableSeeder::class);
        $this->call(QuestionTableSeeder::class);
        $this->call(PossibleAnswerTableSeeder::class);
        $this->call(UserQuestionnaireSeeder::class);

        $this->command->info("Database seeded.");

        // Re Guard model
        Eloquent::reguard();
    }
}

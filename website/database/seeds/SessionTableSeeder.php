<?php

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Questionnaire;
use App\Models\Session;
use App\Models\Theme;

class SessionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Creating 3 sessions.");

        // Create the Session
        factory(Session::class, 3)->create();

        // Get all the themes attaching up to 3 random themes to each session
        $themes = Theme::all();

        // Get all the courses attaching up to 3 random courses to each session
        $courses = Course::all();

        // Get all the questionnaires attaching up to 3 random questionnaires to each session
        $questionnaires = Questionnaire::all();

        // Populate the pivot table
        Session::all()->each(function ($session) use ($themes, $courses, $questionnaires) {
            /** @var Session $session */
            $session->themes()->attach(
                $themes->random(rand(1, 3))->pluck('id')->toArray()
            );
            $session->courses()->attach(
                $courses->random(rand(1, 3))->pluck('id')->toArray(),
                ['position' => 1]
            );
            $session->questionnaires()->attach(
                $questionnaires->random(rand(1, 3))->pluck('id')->toArray(),
                ['position' => 1]
            );
        });

        $this->command->info('Sessions Created!');
    }
}

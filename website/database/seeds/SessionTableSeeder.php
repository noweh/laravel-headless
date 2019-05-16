<?php

use Illuminate\Database\Seeder;
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

        // Populate the pivot table
        Session::all()->each(function ($session) use ($themes) {
            /** @var Session $session */
            $session->themes()->attach(
                $themes->random(rand(1, 3))->pluck('id')->toArray()
            );
        });

        $this->command->info('Sessions Created!');
    }
}

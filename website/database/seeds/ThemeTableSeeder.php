<?php

use Illuminate\Database\Seeder;
use App\Models\Theme;

class ThemeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Creating 5 themes.");

        // Create the Theme
        factory(Theme::class, 5)->create();

        $this->command->info('Themes Created!');
    }
}

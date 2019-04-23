<?php

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Theme;

class ModuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Creating 3 modules.");

        // Create the Module
        factory(Module::class, 3)->create();

        // Get all the themes attaching up to 3 random themes to each module
        $themes = Theme::all();

        // Populate the pivot table
        Module::all()->each(function ($module) use ($themes) {
            /** @var Module $module */
            $module->themes()->attach(
                $themes->random(rand(1, 3))->pluck('id')->toArray()
            );
        });

        $this->command->info('Modules Created!');
    }
}

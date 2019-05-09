<?php

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Theme;

class CourseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Creating 3 courses.");

        // Create the Course
        factory(Course::class, 3)->create();

        // Get all the themes attaching up to 3 random themes to each course
        $themes = Theme::all();

        // Populate the pivot table
        Course::all()->each(function ($course) use ($themes) {
            /** @var Course $course */
            $course->themes()->attach(
                $themes->random(rand(1, 3))->pluck('id')->toArray(),
                ['position' => 1]
            );
        });

        $this->command->info('Courses Created!');
    }
}

<?php

use Illuminate\Database\Seeder;
use App\Models\QuestionType;

class QuestionTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Creating 2 question types.");

        // Create the Theme
        QuestionType::create([
            'published' => true,
            'code' => 'qcm',
            'active:fr' => true,
            'label:fr' => 'QCM',
            'active:en' => true,
            'label:en' => 'MCQ'
        ]);

        QuestionType::create([
            'published' => true,
            'code' => 'vrai/faux',
            'active:fr' => true,
            'label:fr' => 'Vrai/Faux',
            'active:en' => true,
            'label:en' => 'True/False'
        ]);

        $this->command->info('Question Types Created!');
    }
}

<?php

use Illuminate\Database\Seeder;
use App\Models\AdminUser;

class AdminUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Creating two admin users.");

        // Create a first User
        AdminUser::create([
            'is_superadmin' => true,
            'name' => 'Julien Schmitt',
            'email' => 'julien.schmitt@mazarinedigital.com',
            'password' => Hash::make('julien.schmitt')
        ]);

        // Create a first User
        AdminUser::create([
            'is_superadmin' => false,
            'name' => 'Simple user',
            'email' => 'simple.user@mazarinedigital.com',
            'password' => Hash::make('simple.user')
        ]);

        $this->command->info('Admin users Created!');
    }
}

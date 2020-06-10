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
        $this->command->info("Creating admin users.");

        // Create a first User
        AdminUser::create([
            'is_superadmin' => true,
            'first_name' => 'Julien',
            'last_name' => 'SCHMITT',
            'email' => 'julien.schmitt@mazarinedigital.com',
            'password' => Hash::make('julien.schmitt')
        ]);

        $this->command->info('Admin users Created!');
    }
}

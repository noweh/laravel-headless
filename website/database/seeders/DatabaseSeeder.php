<?php

namespace Database\Seeders;

use Eloquent;
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

        $this->call(AdminUserTableSeeder::class);
        
        $this->command->info("Database seeded.");

        // Re Guard model
        Eloquent::reguard();
    }
}

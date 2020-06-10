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

        $this->call(AdminUserTableSeeder::class);
        $this->call(FakeDataSeeder::class);
        
        $this->command->info("Database seeded.");

        // Re Guard model
        Eloquent::reguard();
    }
}

<?php

use App\Models\AdminUser;
use App\Models\Client;
use Illuminate\Database\Seeder;
use App\Models\Show;

class FakeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Creating fake data.");

        $clientDior = Client::create([
            'name' => 'Dior',
            'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a8/Dior_Logo.svg/200px-Dior_Logo.svg.png'
        ]);

        $clientChanel = Client::create([
            'name' => 'Chanel',
            'logo_url' => 'https://www.chanel.com/wfj/img/logo.svg'
        ]);

        $clientOpera = Client::create([
            'name' => 'Opera',
            'logo_url' => 'https://agence.conseilspreference.com/img/prj/opera-national-de-paris-logo-blanc.png'
        ]);

        $clientOther = Client::create([
            'name' => 'Autre Client'
        ]);

        // Create a first User
        AdminUser::create([
            'is_superadmin' => false,
            'first_name' => 'Simple',
            'last_name' => 'User',
            'email' => 'simple.user@mazarinedigital.com',
            'password' => Hash::make('simple.user'),
            'client_id' => $clientDior->client_id
        ]);

        // Create a first User
        AdminUser::create([
            'is_superadmin' => false,
            'first_name' => 'Compte',
            'last_name' => 'Opera',
            'email' => 'simple.user@operadeparis.fr',
            'password' => Hash::make('simple.user'),
            'client_id' => $clientOpera->client_id
        ]);

        // Create shows
        $show1 = Show::create([
            'client_id' => $clientDior->client_id,
            'title' => 'défilé 1'
        ]);
        $show2 = Show::create([
            'client_id' => $clientDior->client_id,
            'title' => 'défilé 2'
        ]);
        $show3 = Show::create([
            'client_id' => $clientOther->client_id,
            'title' => 'défilé 3'
        ]);
        $show4 = Show::create([
            'client_id' => $clientOpera->client_id,
            'title' => 'vidéo Opéra'
        ]);
        $this->command->info('Fake data Created!');
    }
}

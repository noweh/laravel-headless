<?php

namespace App\Providers;

use Auth;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Helpers\Secutix\Extensions\SecutixUserProvider;
use App\Services\Auth\SecutixGuard;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        //'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        // add custom guard provider
        Auth::provider('secutix_users', function ($app, array $config) {
            return new SecutixUserProvider($app->make(User::class));
        });
     
        // add custom guard
        Auth::extend('frontstage', function ($app, $name, array $config) {
            return new SecutixGuard(Auth::createUserProvider($config['provider']), $app->make('request'));
        });
    }
}

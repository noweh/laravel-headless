<?php

namespace App\Providers;

use Schema;
use Illuminate\Support\ServiceProvider;
use Config;
use View;
use URL;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() != 'production') {
            /**
             * Loader for registering facades.
             */
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();

            /*
            * Load third party local providers
            */

            if (class_exists('\\Barryvdh\\Debugbar\\ServiceProvider')) {
                $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
                $loader->alias('Debugbar', \Barryvdh\Debugbar\Facade::class);
            }
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('phone_number', 'App\\Rules\\PhoneNumber@passes');
        Validator::extend('is_unique', 'App\\Rules\\IsUnique@passes');
        View::share('languages', array_keys(Config::get('app.locales', [])));
        View::share('language_names', Config::get('app.locale_names', []));
        Schema::defaultStringLength(191);
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Config;
use View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // This service provider is a great spot to register your various container
        // bindings with the application. As you can see, we are registering our
        // "Registrar" implementation here. You can add your own bindings too!
        $this->app->bind(
            'App\Contracts\Repositories\CourseRepositoryInterface',
            'App\Repositories\CourseRepository'
        );

        $this->app->bind(
            'App\Contracts\Repositories\ModuleRepositoryInterface',
            'App\Repositories\ModuleRepository'
        );

        $this->app->bind(
            'App\Contracts\Repositories\PossibleAnswerRepositoryInterface',
            'App\Repositories\PossibleAnswerRepository'
        );

        $this->app->bind(
            'App\Contracts\Repositories\QuestionnaireRepositoryInterface',
            'App\Repositories\QuestionnaireRepository'
        );

        $this->app->bind(
            'App\Contracts\Repositories\QuestionRepositoryInterface',
            'App\Repositories\QuestionRepository'
        );

        $this->app->bind(
            'App\Contracts\Repositories\QuestionTypeRepositoryInterface',
            'App\Repositories\QuestionTypeRepository'
        );

        $this->app->bind(
            'App\Contracts\Repositories\ThemeRepositoryInterface',
            'App\Repositories\ThemeRepository'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::share('languages', array_keys(Config::get('app.locales', [])));
        View::share('language_names', Config::get('app.locale_names', []));
    }
}

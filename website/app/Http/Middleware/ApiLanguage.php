<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;

class ApiLanguage
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function handle(Request $request, Closure $next)
    {
        if (($locale = $request->input('lang')) && array_key_exists($locale, $this->app->config->get('app.locales'))) {
            $this->app->setLocale($locale);
        } else {
            $this->app->setLocale($this->app->config->get('app.fallback_locale'));
        }

        return $next($request);
    }
}

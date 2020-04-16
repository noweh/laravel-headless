<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $allowOrigin = '*';
        $allowHeaders = '*';
        $allowMethods = 'GET, POST, PUT, PATCH, DELETE, OPTIONS';
        $headers = [];
        foreach ($request->headers->all() as $header => $values) {
            $header = str_replace(' ', '-', ucwords(str_replace('-', ' ', $header)));
            if ('Access-Control-Request-Headers' == $header) {
                $tmp = explode(',', $values[0]);
                foreach ($tmp as $tmpItem) {
                    $headers[] = str_replace(' ', '-', ucwords(str_replace('-', ' ', trim($tmpItem))));
                }
                $allowHeaders = join(', ', $headers);
            }
            if ('Access-Control-Request-Method' == $header) {
                $allowMethods = $values[0];
            }
            if ('Origin' == $header) {
                $allowOrigin = $values[0];
            }

        }
        return $next($request)
            ->header('Referrer-Policy', 'origin-when-cross-origin')
            ->header('Access-Control-Allow-Origin', $allowOrigin)
            ->header('Access-Control-Allow-Methods', $allowMethods)
            ->header('Access-Control-Allow-Headers', $allowHeaders)
            ->header('Access-Control-Allow-Credentials', 'true')
        ;
    }
}

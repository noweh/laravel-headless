<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Closure;

class HeaderCacheControl
{
    public function handle(Request $request, Closure $next, $maxAge = 0)
    {
        $response = $next($request);

        if ($request->method() == 'GET' && intval($maxAge) && (!request('mode') || request('mode') != 'contribution')) {
            /* Convert max age minutes to seconds */
            $maxAge = intval($maxAge) * 60;

            $response = $response instanceof Response ? $response : response($response);
            $response->headers->set('cache-control', 'max-age=' . $maxAge . ', public');
        }

        return $response;
    }
}

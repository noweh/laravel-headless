<?php

namespace App\Http\Middleware;

use App\Services\ParseInputStream;
use Closure;

class ParseMultipartFormDataInputForNonPostRequests
{
    /*
     * Content-Type: multipart/form-data - only works for POST requests. All others fail, this is a bug in PHP since 2011.
     * See comments here: https://github.com/laravel/framework/issues/13457
     *
     * This middleware converts all multi-part/form-data for NON-POST requests, into a properly formatted
     * request variable for Laravel 5.6. It uses the ParseInputStream class, found here:
     * https://gist.github.com/devmycloud/df28012101fbc55d8de1737762b70348
     */
    public function handle($request, Closure $next)
    {
        if ($request->method() == 'POST' || $request->method() == 'GET') {
            return $next($request);
        }

        if (preg_match('/multipart\/form-data/', $request->headers->get('Content-Type')) or
            preg_match('/multipart\/form-data/', $request->headers->get('content-type'))
        ) {
            $params = array();
            new ParseInputStream($params);
            $files = array();
            $parameters = array();
            foreach ($params as $key => $param) {
                if ($param instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                    $files[$key] = $param;
                } else {
                    $parameters[$key] = $param;
                }
            }
            if (count($files) > 0) {
                $request->files->add($files);
            }
            if (count($parameters) > 0) {
                $request->request->add($parameters);
            }
        }
        return $next($request);
    }
}

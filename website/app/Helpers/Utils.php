<?php

use Cocur\Slugify\Slugify;
use Illuminate\Http\Request;

if (!function_exists('array_unique_recursive')) {
    function array_unique_recursive($array)
    {
        if (count($array) == count($array, COUNT_RECURSIVE)) {
            $array = array_unique($array, SORT_REGULAR);
        }

        foreach ($array as $key => $elem) {
            if (is_array($elem)) {
                $array[$key] = array_unique_recursive($elem);
            }
        }

        return $array;
    }
}

if (!function_exists('routeLocalize')) {
    function routeLocalize($routeName, $parameters = [], $locale = null, $absolute = true, $forceUrlLocale = false)
    {
        if ($routeName=="#") {
            return $routeName;
        }
        if (substr($routeName, 0, 4) == "http") {
            return $routeName;
        }

        #find generic routeName
        if ($routeName[2]=='.' && in_array(substr($routeName, 0, 2), array_keys(config('app.locales')))) {
            $routeName = substr($routeName, 3);
        }

        $locale = ($locale==null) ? App::getLocale() : $locale;
        $routeName = ($locale==config('app.fallback_locale') && !$forceUrlLocale) ? $routeName : $locale.'.'.$routeName;

        return route($routeName, $parameters, $absolute);
    }
}

if (!function_exists('sluggify')) {
    function sluggify($str)
    {
        // Remove &nbsp; from str
        $str = str_replace('&nbsp;', ' ', $str);

        if ('fr' == App::getLocale()) {
            $rulesetLanguage = 'french';
        } else {
            $rulesetLanguage = 'english';
        }
        $slugify = new Slugify;
        return $slugify->slugify($str, ['ruleset' => $rulesetLanguage]);
    }
}

if (!function_exists('array_has_same_keys')) {
    function array_has_same_keys(array $master, array $candidate)
    {
        foreach (array_keys($master) as $k) {
            if (!array_key_exists($k, $candidate)) {
                echo 'not found ', $k , ' in candidate', PHP_EOL;
                return false;
            }
        }
        foreach (array_keys($candidate) as $k) {
            if (!array_key_exists($k, $master)) {
                echo 'not found ', $k , ' in master', PHP_EOL;
                return false;
            }
        }
        return true;
    }
}

if (!function_exists('formatDataForPagination')) {
    function formatDataForPagination($data)
    {
        return [
            'data' => $data['data'],
            'links' => [
                'first' => $data['first_page_url'],
                'last' => $data['last_page_url'],
                'prev' => $data['prev_page_url'],
                'next' => $data['next_page_url']
            ],
            'meta' => [
                'current_page' => $data['current_page'],
                'from' => $data['from'],
                'last_page' => $data['last_page'],
                'path' => $data['path'],
                'per_page' => $data['per_page'],
                'to' => $data['to'],
                'total' => $data['total']
            ]
        ];
    }
}

if (!function_exists('dumpQueries')) {
    function dumpQueries()
    {
        $queries = app('debugbar')->getData()['queries'];
        echo 'nb queries=', $queries['nb_statements'];
        foreach ($queries['statements'] as $statement) {
            echo $statement['sql'], ' ', print_r($statement['bindings'], true), PHP_EOL;
        }
    }
}

if (!function_exists('formatJsonWithHeaders')) {
    function formatJsonWithHeaders($data, $maxAge = 0)
    {
        // CPU Optimizations
        header('Content-Type: application/json');

        if (request()->method() == 'GET' && intval($maxAge) &&
            (!request('mode') || request('mode') != 'contribution')) {
            header('Cache-Control: max-age='.$maxAge.', public');
        }

        $allowOrigin = '*';
        $allowHeaders = '*';
        $allowMethods = 'GET, POST, PUT, PATCH, DELETE, OPTIONS';
        $headers = [];
        foreach (request()->headers->all() as $header => $values) {
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
        header('Referrer-Policy: origin-when-cross-origin');
        header('Access-Control-Allow-Origin: ' . $allowOrigin);
        header('Access-Control-Allow-Methods: '. $allowMethods);
        header('Access-Control-Allow-Headers: ' . $allowHeaders);
        header('Access-Control-Allow-Credentials: true');

        if (!key_exists('data', $data)) {
            $data = ['data' => $data];
        }

        if (app()->bound('debugbar') && app('debugbar')->isEnabled()) {
            $data += [
                '_debugbar' => app('debugbar')->getData()
            ];
        }

        $encoded = json_encode($data);
        header('Content-Length: ' . strlen($encoded));
        echo $encoded;
        flush();
        exit;
    }
}

if (!function_exists('getEnvFromUrl')) {
    function getEnvFromUrl(Request $request)
    {
        $host = $request->getHttpHost();
        if('aria.operadeparis.fr' == $host) {
            $ret = '';
        } else {
            $possible = ['dev', 'local', 'test', 'int', 'uat', 'oat', 'staging', 'preprod'];
            $localPossible = ['xip.io', 'nip.io'];
            $hostParts = explode('.', $host);
            $subdomainParts = explode('-', $hostParts[0]);
            $envCandidate = array_pop($subdomainParts);
            if (in_array($envCandidate, $possible)) {
                $ret = $envCandidate;
            } else {
                if (in_array($envCandidate = array_pop($hostParts), $possible)) {
                    $ret = $envCandidate;
                } else {
                    if (in_array(array_pop($hostParts) . '.' . $envCandidate, $localPossible)) {
                        $ret = 'dev';
                    } else {
                        $ret = '';
                    }
                }
            }
        }

        return $ret;
    }
}
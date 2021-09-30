<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;

class SettingController extends AbstractController
{
    /**
     * Route for options with Cors
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function options()
    {
        return response(null, Response::HTTP_NO_CONTENT);
    }
}

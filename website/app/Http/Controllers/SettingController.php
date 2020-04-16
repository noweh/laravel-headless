<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Badge;
use App\Models\Card;
use App\Models\Course;
use App\Models\MediaLibrary;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\Session;
use App\Models\Story;
use App\Models\Transition;
use App\Models\VideoLibrary;
use Config;
use Lang;
use Symfony\Component\HttpFoundation\Response;

class SettingController extends AbstractController
{
    /**
     * @OA\Get(
     *     path="/settings",
     *     tags={"Setting"},
     *     summary="Retrieve Settings.",
     *     description="Returns Settings.",
     *     operationId="getSettings",
     *     @OA\Parameter(
     *         name="lang",
     *         required=true,
     *         in="query",
     *         description="Language",
     *         @OA\Schema(
     *             type="string",
     *             enum={"fr","en"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Display Settings.",
     *         @OA\Schema(
     *             type="array"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error."
     *     )
     * )
     */
    public function index()
    {
        $lang = request('lang');
        $data = new \stdClass();

        $data->cloudinary = new \stdClass();
        $data->cloudinary->images_definition = MediaLibrary::formatDefinition();
        
        $data->orientations = MediaLibrary::orientationDefinition();
        
        $return = new \stdClass();
        $return->data = $data;
        return response()->json($return);
    }

    /**
     * Route for options with Cors
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function options()
    {
        return response(null, Response::HTTP_NO_CONTENT);
    }
}

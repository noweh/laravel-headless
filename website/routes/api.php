<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group(['domain' => env('DOMAINE_API')], function () {
    Route::group(['prefix' => 'v1'], function () {
        Route::group(['middleware' => ['api.language']], function () {
            Route::apiResource('courses', 'CourseController');
            Route::apiResource('modules', 'ModuleController');
            Route::apiResource('possible-answers', 'PossibleAnswerController');
            Route::apiResource('questions', 'QuestionController');
            Route::apiResource('questionnaires', 'QuestionnaireController');
            Route::apiResource('question-types', 'QuestionTypeController');
            Route::apiResource('themes', 'ThemeController');
        });
    });
});

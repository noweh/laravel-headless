<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Resources\QuestionnaireResource;
use App\Models\Questionnaire;

Route::get('/', function () {
    App::setLocale('fr');
    $questionnaireRessources = QuestionnaireResource::collection(
        Questionnaire::with([
            //'themes',
            //'questions',
            //'questions.possibleAnswers',
            //'questions.goodAnswer'
        ])->paginate(25)
    );

    return $questionnaireRessources;
});

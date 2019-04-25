<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuestionnaireResource;
use App\Models\Questionnaire;
use App\Models\Theme;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return QuestionnaireResource::collection(
            Questionnaire::with([
                //'themes',
                //'questions',
                //'questions.possibleAnswers',
                //'questions.goodAnswer'
            ])->paginate(25)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return QuestionnaireResource
     */
    public function store(Request $request)
    {
        $theme = Theme::where('code', $request->theme_code)->firstOrFail();

        /*if (!$theme) {
            return response()->json([
                'error' => 'Theme not found'
            ], 404);
        }*/

        $questionnaire = Questionnaire::create([
            'published' => $request->published,
            'level' => $request->level,
            'note_max' => $request->note_max,
            'active:fr' => $request->{'active:fr'},
            'title:fr' => $request->{'title:fr'},
            'description:fr' => $request->{'description:fr'},
            'active:en' => $request->{'active:en'},
            'title:en' => $request->{'title:en'},
            'description:en' => $request->{'description:en'},
        ]);

        $questionnaire->themes()->attach($theme->toArray());

        return new QuestionnaireResource($questionnaire);
    }

    /**
     * Display the specified resource.
     *
     * @param  Questionnaire  $questionnaire
     * @return QuestionnaireResource
     */
    public function show(Questionnaire $questionnaire)
    {
        return new QuestionnaireResource($questionnaire);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Questionnaire $questionnaire
     * @return QuestionnaireResource
     */
    public function update(Request $request, Questionnaire $questionnaire)
    {
        $questionnaire->update($request);

        return new QuestionnaireResource($questionnaire);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Questionnaire $questionnaire
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Questionnaire $questionnaire)
    {
        $questionnaire->delete();

        return response()->json(null, 204);
    }
}

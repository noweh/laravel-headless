<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\QuestionnaireRepositoryInterface;
use App\Services\Validation\QuestionnaireValidator;

class QuestionnaireController extends AbstractController
{
    public function __construct(
        QuestionnaireRepositoryInterface $repository,
        QuestionnaireValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;

        parent::__construct();
    }

    /**
     * @OA\Get(
     *     path="/questionnaires",
     *     tags={"Questionnaire"},
     *     summary="Retrieve a listing of Questionnaires.",
     *     description="Returns a listing of Questionnaires.",
     *     operationId="getQuestionnaires",
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
     *     @OA\Parameter(
     *         name="include",
     *         required=false,
     *         in="query",
     *         description="Include relationship in results. String=themes,questions,questions.possibleAnswers,questions.goodAnswer",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="published",
     *         required=false,
     *         in="query",
     *         description="Filter on published/not published elements",
     *         @OA\Schema(
     *             type="boolean",
     *             enum={true,false}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         required=false,
     *         in="query",
     *         description="Name of attribute to ORDER BY",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sortOrder",
     *         required=false,
     *         in="query",
     *         description="Clause for the ORDER BY",
     *         @OA\Schema(
     *             type="string",
     *             enum={"asc","desc"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Display a listing of Questionnaires.",
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Questionnaire")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity. Undefined method or relationship."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error."
     *     )
     * )
     */

    /**
     * @OA\Get(
     *     path="/questionnaires/{questionnaireId}",
     *     tags={"Questionnaire"},
     *     summary="Find a Questionnaire by ID.",
     *     description="Returns a single Questionnaire.",
     *     operationId="getQuestionnaireById",
     *     @OA\Parameter(
     *         name="questionnaireId",
     *         in="path",
     *         description="ID of Questionnaire to return",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
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
     *     @OA\Parameter(
     *         name="include",
     *         required=false,
     *         in="query",
     *         description="Include relationship in results. String=themes,questions,questions.possibleAnswers,questions.goodAnswer",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Questionnaire")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity. Undefined method or relationship."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error."
     *     )
     * )
     */

    /**
     * @OA\Post(
     *     path="/questionnaires",
     *     tags={"Questionnaire"},
     *     summary="Add a new Questionnaire",
     *     operationId="addQuestionnaire",
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(ref="#/components/schemas/Questionnaire")
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Invalid input"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error."
     *     ),
     *     @OA\RequestBody(
     *         description="Create a Questionnaire object",
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="published",
     *                 description="Create a published value",
     *                 type="boolean",
     *                 default=true
     *             ),
     *             @OA\Property(
     *                 property="level",
     *                 description="Create a level value",
     *                 type="integer",
     *                 enum={1, 2, 3},
     *                 default=2
     *             ),
     *             @OA\Property(
     *                 property="active_fr",
     *                 description="Create a active_fr value",
     *                 type="boolean",
     *                 default=true
     *             ),
     *             @OA\Property(
     *                 property="title_fr",
     *                 description="Create a title_fr value",
     *                 type="string",
     *                 example="un titre par défaut"
     *             ),
     *             @OA\Property(
     *                 property="description_fr",
     *                 description="Create a description_fr value",
     *                 type="string",
     *                 example="une description par défaut"
     *             ),
     *             @OA\Property(
     *                 property="active_en",
     *                 description="Updated active_en value",
     *                 type="boolean",
     *                 default=true
     *             ),
     *             @OA\Property(
     *                 property="title_en",
     *                 description="Create a title_en value",
     *                 type="string",
     *                 example="a default title"
     *             ),
     *             @OA\Property(
     *                 property="description_en",
     *                 description="Create a description_en value",
     *                 type="string",
     *                 example="a default description"
     *             ),
     *             @OA\Property(
     *                 property="themes_id",
     *                 description="Create a themes_id values",
     *                 type="array",
     *                 @OA\Items(
     *                     type="integer"
     *                 ),
     *                 example={1,2}
     *             ),
     *             @OA\Property(
     *                 property="questions_id",
     *                 description="Create a questions_id values",
     *                 type="array",
     *                 @OA\Items(
     *                     type="integer"
     *                 ),
     *                 example={2,5}
     *             )
     *         )
     *     )
     * )
     */

    /**
     * @OA\Patch(
     *     path="/questionnaires/{questionnaireId}",
     *     tags={"Questionnaire"},
     *     summary="Update Questionnaire by ID",
     *     description="Update a single Questionnaire.",
     *     operationId="updateQuestionnaire",
     *     @OA\Parameter(
     *         name="questionnaireId",
     *         in="path",
     *         description="ID of Questionnaire that to be updated",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
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
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Questionnaire")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity. Undefined method or relationship."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error."
     *     ),
     *     @OA\RequestBody(
     *         description="Updated Questionnaire object",
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="published",
     *                 description="Updated published value",
     *                 type="boolean",
     *                 default=true
     *             ),
     *             @OA\Property(
     *                 property="level",
     *                 description="Updated level value",
     *                 type="integer",
     *                 enum={1, 2, 3},
     *                 default=2
     *             ),
     *             @OA\Property(
     *                 property="active",
     *                 description="Updated active value",
     *                 type="boolean",
     *                 default=true
     *             ),
     *             @OA\Property(
     *                 property="title",
     *                 description="Updated title value",
     *                 type="string",
     *                 example="un titre par défaut"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 description="Updated description value",
     *                 type="string",
     *                 example="une description par défaut"
     *             ),
     *             @OA\Property(
     *                 property="themes_id",
     *                 description="Updated themes_id values",
     *                 type="array",
     *                 @OA\Items(
     *                     type="integer"
     *                 ),
     *                 example={1,2}
     *             ),
     *             @OA\Property(
     *                 property="questions_id",
     *                 description="Updated questions_id values",
     *                 type="array",
     *                 @OA\Items(
     *                     type="integer"
     *                 ),
     *                 example={2,5}
     *             )
     *         )
     *     )
     * )
     */

    /**
     * @OA\Delete(
     *     path="/questionnaires/{questionnaireId}",
     *     tags={"Questionnaire"},
     *     summary="Delete Questionnaire by ID.",
     *     description="Delete a single Questionnaire.",
     *     operationId="deleteQuestionnaireById",
     *     @OA\Parameter(
     *         name="questionnaireId",
     *         in="path",
     *         description="ID of Questionnaire that needs to be deleted",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="successful operation",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity. Undefined method or relationship."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error."
     *     )
     * )
     */
}

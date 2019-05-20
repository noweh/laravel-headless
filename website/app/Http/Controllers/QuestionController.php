<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Services\Validation\QuestionValidator;

class QuestionController extends AbstractController
{
    public function __construct(
        QuestionRepositoryInterface $repository,
        QuestionValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;

        parent::__construct();
    }

    /**
     * @OA\Get(
     *     path="/questions",
     *     tags={"Question"},
     *     summary="Retrieve a listing of Questions.",
     *     description="Returns a listing of Questions.",
     *     operationId="getQuestions",
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
     *         description="Include relationship in results. String=questionnaires,possibleAnswers,goodAnswer",
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
     *         description="Display a listing of Questions.",
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Question")
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
     *     path="/questions/{questionId}",
     *     tags={"Question"},
     *     summary="Find a Question by ID.",
     *     description="Returns a single Question.",
     *     operationId="getQuestionById",
     *     @OA\Parameter(
     *         name="questionId",
     *         in="path",
     *         description="ID of Question to return",
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
     *         description="Include relationship in results. String=questionnaires,possibleAnswers,goodAnswer",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Question")
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
     *     path="/questions",
     *     tags={"Question"},
     *     summary="Add a new Question",
     *     operationId="addQuestion",
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(ref="#/components/schemas/Question")
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
     *         description="Create a Question object",
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
     *                 property="format",
     *                 description="Updated level value",
     *                 type="string",
     *                 enum={"text", "image", "video"},
     *                 default="text"
     *             ),
     *             @OA\Property(
     *                 property="duration_min",
     *                 description="Create a duration_min value",
     *                 type="integer",
     *                 default=2
     *             ),
     *             @OA\Property(
     *                 property="duration_max",
     *                 description="Create a duration_max value",
     *                 type="integer",
     *                 default=10
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
     *                 property="question_type_id",
     *                 description="Create a question_type_id value",
     *                 type="integer",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="good_answer_id",
     *                 description="Create a good_answer_id value",
     *                 type="integer",
     *                 example=1
     *             )
     *         )
     *     )
     * )
     */

    /**
     * @OA\Patch(
     *     path="/questions/{questionId}",
     *     tags={"Question"},
     *     summary="Update Question by ID",
     *     description="Update a single Question.",
     *     operationId="updateQuestion",
     *     @OA\Parameter(
     *         name="questionId",
     *         in="path",
     *         description="ID of Question that to be updated",
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
     *         @OA\JsonContent(ref="#/components/schemas/Question")
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
     *         description="Updated Question object",
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
     *                 property="format",
     *                 description="Updated level value",
     *                 type="string",
     *                 enum={"text", "image", "video"},
     *                 default="text"
     *             ),
     *             @OA\Property(
     *                 property="duration_min",
     *                 description="Create a duration_min value",
     *                 type="integer",
     *                 default=2
     *             ),
     *             @OA\Property(
     *                 property="duration_max",
     *                 description="Create a duration_max value",
     *                 type="integer",
     *                 default=10
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
     *                 property="question_type_id",
     *                 description="Create a question_type_id value",
     *                 type="integer",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="good_answer_id",
     *                 description="Create a good_answer_id value",
     *                 type="integer",
     *                 example=1
     *             )
     *         )
     *     )
     * )
     */

    /**
     * @OA\Delete(
     *     path="/questions/{questionId}",
     *     tags={"Question"},
     *     summary="Delete Question by ID.",
     *     description="Delete a single Question.",
     *     operationId="deleteQuestionById",
     *     @OA\Parameter(
     *         name="questionId",
     *         in="path",
     *         description="ID of Question that needs to be deleted",
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

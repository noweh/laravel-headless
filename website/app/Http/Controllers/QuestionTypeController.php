<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\QuestionTypeRepositoryInterface;
use App\Services\Validation\QuestionTypeValidator;

class QuestionTypeController extends AbstractController
{
    public function __construct(
        QuestionTypeRepositoryInterface $repository,
        QuestionTypeValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;

        parent::__construct();
    }

    /**
     * @OA\Get(
     *     path="/questionTypes",
     *     tags={"QuestionType"},
     *     summary="Retrieve a listing of QuestionTypes.",
     *     description="Returns a listing of QuestionTypes.",
     *     operationId="getQuestionTypes",
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
     *         description="Include relationship in results. String=questions",
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
     *         description="Display a listing of QuestionTypes.",
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/QuestionType")
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
     *     path="/questionTypes/{questionTypeId}",
     *     tags={"QuestionType"},
     *     summary="Find a QuestionType by ID.",
     *     description="Returns a single QuestionType.",
     *     operationId="getQuestionTypeById",
     *     @OA\Parameter(
     *         name="questionTypeId",
     *         in="path",
     *         description="ID of QuestionType to return",
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
     *         description="Include relationship in results. String=questions",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/QuestionType")
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
     *     path="/questionTypes",
     *     tags={"QuestionType"},
     *     summary="Add a new QuestionType",
     *     operationId="addQuestionType",
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(ref="#/components/schemas/QuestionType")
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
     *         description="Create a QuestionType object",
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
     *                 property="code",
     *                 description="Create a code value",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="active_fr",
     *                 description="Create a active_fr value",
     *                 type="boolean",
     *                 default=true
     *             ),
     *             @OA\Property(
     *                 property="label_fr",
     *                 description="Create a label_fr value",
     *                 type="string",
     *                 example="un label par défaut"
     *             ),
     *             @OA\Property(
     *                 property="active_en",
     *                 description="Updated active_en value",
     *                 type="boolean",
     *                 default=true
     *             ),
     *             @OA\Property(
     *                 property="label_en",
     *                 description="Create a label_en value",
     *                 type="string",
     *                 example="a default label"
     *             )
     *         )
     *     )
     * )
     */

    /**
     * @OA\Patch(
     *     path="/questionTypes/{questionTypeId}",
     *     tags={"QuestionType"},
     *     summary="Update QuestionType by ID",
     *     description="Update a single QuestionType.",
     *     operationId="updateQuestionType",
     *     @OA\Parameter(
     *         name="questionTypeId",
     *         in="path",
     *         description="ID of QuestionType that to be updated",
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
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/QuestionType")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid ID supplied",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error."
     *     ),
     *     @OA\RequestBody(
     *         description="Updated QuestionType object",
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
     *                 property="code",
     *                 description="Updated code value",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="active",
     *                 description="Updated active value",
     *                 type="boolean",
     *                 default=true
     *             ),
     *             @OA\Property(
     *                 property="label",
     *                 description="Updated label value",
     *                 type="string",
     *                 example="un label par défaut"
     *             )
     *         )
     *     )
     * )
     */

    /**
     * @OA\Delete(
     *     path="/questionTypes/{questionTypeId}",
     *     tags={"QuestionType"},
     *     summary="Delete QuestionType by ID.",
     *     description="Delete a single QuestionType.",
     *     operationId="deleteQuestionTypeById",
     *     @OA\Parameter(
     *         name="questionTypeId",
     *         in="path",
     *         description="ID of QuestionType that needs to be deleted",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid ID supplied",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error."
     *     )
     * )
     */
}

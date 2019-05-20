<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\SessionRepositoryInterface;
use App\Services\Validation\SessionValidator;

class SessionController extends AbstractController
{
    public function __construct(
        SessionRepositoryInterface $repository,
        SessionValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;

        parent::__construct();
    }

    /**
     * @OA\Get(
     *     path="/sessions",
     *     tags={"Session"},
     *     summary="Retrieve a listing of Sessions.",
     *     description="Returns a listing of Sessions.",
     *     operationId="getSessions",
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
     *         description="Include relationship in results. String=themes,questionnaires,courses",
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
     *         description="Display a listing of Sessions.",
     *         @OA\Schema(ref="#/components/schemas/Session")
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
     *     path="/sessions/{sessionId}",
     *     tags={"Session"},
     *     summary="Find a Session by ID.",
     *     description="Returns a single Session.",
     *     operationId="getSessionById",
     *     @OA\Parameter(
     *         name="sessionId",
     *         in="path",
     *         description="ID of Session to return",
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
     *         description="Include relationship in results. String=themes,questionnaires,courses",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Session")
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
     *     path="/sessions",
     *     tags={"Session"},
     *     summary="Add a new Session",
     *     operationId="addSession",
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(ref="#/components/schemas/Session")
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
     *         description="Create a Session object",
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
     *                 property="position",
     *                 description="Create a position value",
     *                 type="integer",
     *                 example=1
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
     *                 property="questionnaires_id",
     *                 description="Create a questionnaires_id values",
     *                 type="array",
     *                 @OA\Items(
     *                     type="integer"
     *                 ),
     *                 example={2,5}
     *             ),
     *             @OA\Property(
     *                 property="courses_id",
     *                 description="Create a courses_id values",
     *                 type="array",
     *                 @OA\Items(
     *                     type="integer"
     *                 ),
     *                 example={3,6}
     *             )
     *         )
     *     )
     * )
     */

    /**
     * @OA\Patch(
     *     path="/sessions/{sessionId}",
     *     tags={"Session"},
     *     summary="Update Session by ID",
     *     description="Update a single Session.",
     *     operationId="updateSession",
     *     @OA\Parameter(
     *         name="sessionId",
     *         in="path",
     *         description="ID of Session that to be updated",
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
     *         @OA\JsonContent(ref="#/components/schemas/Session")
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
     *         description="Updated Session object",
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
     *                 property="position",
     *                 description="Updated position value",
     *                 type="integer",
     *                 example=1
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
     *                 property="questionnaires_id",
     *                 description="Updated questionnaires_id values",
     *                 type="array",
     *                 @OA\Items(
     *                     type="integer"
     *                 ),
     *                 example={2,5}
     *             ),
     *             @OA\Property(
     *                 property="courses_id",
     *                 description="Updated courses_id values",
     *                 type="array",
     *                 @OA\Items(
     *                     type="integer"
     *                 ),
     *                 example={3,6}
     *             )
     *         )
     *     )
     * )
     */

    /**
     * @OA\Delete(
     *     path="/sessions/{sessionId}",
     *     tags={"Session"},
     *     summary="Delete Session by ID.",
     *     description="Delete a single Session.",
     *     operationId="deleteSessionById",
     *     @OA\Parameter(
     *         name="sessionId",
     *         in="path",
     *         description="ID of Session that needs to be deleted",
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

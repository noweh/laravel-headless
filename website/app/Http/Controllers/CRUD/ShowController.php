<?php

namespace App\Http\Controllers\CRUD;

use App\Repositories\ShowRepository;
use App\Services\Validators\ShowValidator;
use Illuminate\Http\Request;

class ShowController extends AbstractCRUDController
{
    /**
     * ShowController constructor.
     * @param ShowRepository $repository
     * @param ShowValidator $validator
     */
    public function __construct(
        ShowRepository $repository,
        ShowValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;

        parent::__construct();
    }

    /**
     * @OA\Get(
     *     path="/shows",
     *     tags={"CRUD\Show"},
     *     summary="Retrieve a listing of Shows.",
     *     description="Returns a listing of Shows.",
     *     operationId="getShows",
     *     @OA\Parameter(
     *         name="lang",
     *         required=false,
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
     *         description="Include relationship in results. String=",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="excludeIds",
     *         required=false,
     *         in="query",
     *         description="Exclude ids in results. Multiple values possible, Separated by a comma",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="fPublished",
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
     *         description="Display a listing of Shows.",
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Show")
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
     *     path="/shows/{showId}",
     *     tags={"CRUD\Show"},
     *     summary="Find a Show by ID.",
     *     description="Returns a single Show.",
     *     operationId="getShowById",
     *     @OA\Parameter(
     *         name="showId",
     *         in="path",
     *         description="ID of Show to return",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="lang",
     *         required=false,
     *         in="query",
     *         description="Language",
     *         @OA\Schema(
     *             type="string",
     *             enum={"fr","en"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="mode",
     *         required=false,
     *         in="query",
     *         description="Display mode",
     *         @OA\Schema(
     *             type="string",
     *             enum={"contribution"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="include",
     *         required=false,
     *         in="query",
     *         description="Include relationship in results. String=",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation.",
     *         @OA\JsonContent(ref="#/components/schemas/Show")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found"
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
     *     path="/shows",
     *     tags={"CRUD\Show"},
     *     summary="Add a new Show",
     *     operationId="addShow",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=201,
     *         description="Created.",
     *         @OA\JsonContent(ref="#/components/schemas/Show")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized."
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Invalid input."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error."
     *     ),
     *     @OA\RequestBody(
     *         description="Create a Show object",
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="published",
     *                 description="Create a published value",
     *                 type="boolean",
     *                 default=true
     *             ),
     *         )
     *     )
     * )
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\AuthenticationException
     * @throws \App\Exceptions\ValidationException
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function store(Request $request)
    {
        // Check if adminUser is authenticated
        $this->getSuperadminUser();

        return parent::store($request);
    }

    /**
     * @OA\Patch(
     *     path="/shows/{showId}",
     *     tags={"CRUD\Show"},
     *     summary="Update Show by ID",
     *     description="Update a single Show.",
     *     operationId="updateShow",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="showId",
     *         in="path",
     *         description="ID of Show that to be updated",
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
     *         description="Successful operation.",
     *         @OA\JsonContent(ref="#/components/schemas/Show")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized."
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
     *         description="Updated Show object",
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="published",
     *                 description="Updated published value",
     *                 type="boolean",
     *                 default=true
     *             )
     *         )
     *     )
     * )
     * @param Request $request
     * @param $itemId
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\AuthenticationException
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function update(Request $request, $itemId)
    {
        // Check if adminUser is authenticated
        $this->getSuperadminUser();

        return parent::update($request, $itemId);
    }

    /**
     * @OA\Delete(
     *     path="/shows/{showId}",
     *     tags={"CRUD\Show"},
     *     summary="Delete Show by ID.",
     *     description="Delete a single Show.",
     *     operationId="deleteShowById",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="showId",
     *         in="path",
     *         description="ID of Show that needs to be deleted",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Successful operation.",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized."
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
     * @param $itemId
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\AuthenticationException
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function destroy($itemId)
    {
        // Check if adminUser is authenticated
        $this->getSuperadminUser();

        return parent::destroy($itemId);
    }
}

<?php

namespace App\Http\Controllers\CRUD;

use App\Exceptions\AuthenticationException;
use App\Repositories\ClientRepository;
use App\Services\Validators\ClientValidator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Exception;

class ClientController extends AbstractCRUDController
{
    public function __construct(
        ClientRepository $repository,
        ClientValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        parent::__construct();
    }

    /**
     * @return array
     * @throws \App\Exceptions\AuthenticationException
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function getScopeQueries()
    {
        $scopeQueries = parent::getScopeQueries();

        $authenticatedUser = $this->getAuthenticatedUser();
        if (!$authenticatedUser->is_superadmin) {
            // If user is not a superadmin, force client_id by its own
            $scopeQueries += ['client_id' => $authenticatedUser->client_id];
        }

        return $scopeQueries;
    }

    /**
     * @OA\Get(
     *     path="/clients",
     *     tags={"CRUD\Client"},
     *     summary="Retrieve a listing of Clients.",
     *     description="Returns a listing of Clients.",
     *     operationId="getClients",
     *     security={{"bearerAuth":{}}},
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
     *         description="Include relationship in results. String=adminUsers,shows",
     *         @OA\Schema(
     *             type="string"
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
     *         description="Display a listing of Clients.",
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Client")
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
     *     path="/clients/{clientId}",
     *     tags={"CRUD\Client"},
     *     summary="Find a Client by ID.",
     *     description="Returns a single Client.",
     *     operationId="getClientById",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="clientId",
     *         in="path",
     *         description="ID of Client to return",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
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
     *         description="Include relationship in results. String=adminUsers,shows",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation.",
     *         @OA\JsonContent(ref="#/components/schemas/Client")
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
     *     path="/clients",
     *     tags={"CRUD\Client"},
     *     summary="Add a new Client",
     *     operationId="addClient",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=201,
     *         description="Created.",
     *         @OA\JsonContent(ref="#/components/schemas/Client")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error."
     *     ),
     *     @OA\RequestBody(
     *         description="Create a Client object",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="logo to upload",
     *                     property="logo",
     *                     type="string",
     *                     format="binary",
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     description="Client's name",
     *                     type="string",
     *                     default="client's name"
     *                 ),
     *                 @OA\Property(
     *                     property="is_activated",
     *                     description="Client's is_activated state",
     *                     type="boolean",
     *                     default=true
     *                 )
     *             )
     *         )
     *     )
     * )
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ValidationException
     * @throws Exception
     */
    public function store(Request $request)
    {
        // Check if AdminUser is authenticated
        $this->getSuperadminUser();

        $newItem = $this->doStoreObject($request);
        $newItem = $this->checkFilesToUpload($newItem->client_id, $request);

        return response()->json(['data' => $this->getResource()::make($newItem)], 201);
    }

    /**
     * @OA\Patch(
     *     path="/clients/{clientId}",
     *     tags={"CRUD\Client"},
     *     summary="Update Client by ID",
     *     description="Update a single Client.",
     *     operationId="updateClient",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="clientId",
     *         in="path",
     *         description="ID of Client that to be updated",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation.",
     *         @OA\JsonContent(ref="#/components/schemas/Client")
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
     *         description="Updated Client object",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="logo to upload",
     *                     property="logo",
     *                     type="string",
     *                     format="binary",
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     description="Client's name",
     *                     type="string",
     *                     default="client's name"
     *                 ),
     *                 @OA\Property(
     *                     property="is_activated",
     *                     description="Client's is_activated state",
     *                     type="boolean",
     *                     default=true
     *                 )
     *             )
     *         )
     *     )
     * )
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthenticationException
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     * @throws \App\Exceptions\ValidationException
     */
    public function update(Request $request)
    {
        $route = \Route::current();

        // Check if adminUser can update this client
        $authenticatedUser = $this->getAuthenticatedUser();
        if (!$authenticatedUser->is_superadmin) {
            if ($authenticatedUser->client_id != last($route->parameters())) {
                throw new AuthenticationException(null, 'Insufficient rights');
            }
        }

        $updatedItem = $this->doUpdateObject($request, last($route->parameters()));
        $updatedItem = $this->checkFilesToUpload($updatedItem->client_id, $request);

        return response()->json(['data' => $this->getResource()::make($updatedItem)]);
    }

    /**
     * @OA\Delete(
     *     path="/clients/{clientId}",
     *     tags={"CRUD\Client"},
     *     summary="Delete Client by ID.",
     *     description="Delete a single Client.",
     *     operationId="deleteClientById",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="clientId",
     *         in="path",
     *         description="ID of Client that needs to be deleted",
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
     * @throws AuthenticationException
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function destroy($itemId)
    {
        // Check if adminUser is authenticated
        $this->getSuperadminUser();

        return parent::destroy($itemId);
    }
}

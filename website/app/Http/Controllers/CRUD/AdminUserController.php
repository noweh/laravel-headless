<?php

namespace App\Http\Controllers\CRUD;

use App\Exceptions\AuthenticationException;
use App\Repositories\AdminUserRepository;
use App\Services\Validators\AdminUserValidator;
use Illuminate\Http\Request;
use Exception;
use Hash;
use Mail;
use App\Mail\Confirmation;

class AdminUserController extends AbstractCRUDController
{
    public function __construct(
        AdminUserRepository $repository,
        AdminUserValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        parent::__construct();
    }

    /**
     * @OA\Get(
     *     path="/adminUsers",
     *     tags={"CRUD\AdminUser"},
     *     summary="Retrieve a listing of AdminUsers.",
     *     description="Returns a listing of AdminUsers.",
     *     operationId="getAdminUsers",
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
     *         description="Display a listing of AdminUsers.",
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/AdminUser")
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
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthenticationException
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function index()
    {
        // Check if adminUser is authenticated
        $this->getSuperadminUser();

        return parent::index();
    }

    /**
     * @OA\Get(
     *     path="/adminUsers/{userId}",
     *     tags={"CRUD\AdminUser"},
     *     summary="Find a AdminUser by ID.",
     *     description="Returns a single AdminUser.",
     *     operationId="getAdminUserById",
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
     *         name="userId",
     *         in="path",
     *         description="ID of AdminUser to return",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation.",
     *         @OA\JsonContent(ref="#/components/schemas/AdminUser")
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
     * @param $itemId
     * @return mixed
     * @throws AuthenticationException
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function show($itemId)
    {
        // Check if AdminUser is authenticated
        $this->getSuperadminUser();

        return parent::show($itemId);
    }

    /**
     * @OA\Post(
     *     path="/adminUsers",
     *     tags={"CRUD\AdminUser"},
     *     summary="Add a new AdminUser",
     *     operationId="addAdminUser",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=201,
     *         description="Created.",
     *         @OA\JsonContent(ref="#/components/schemas/AdminUser")
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
     *         description="Create a AdminUser object",
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="name",
     *                 description="Person's name",
     *                 type="string",
     *                 default="text"
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 description="Person's email",
     *                 type="string",
     *                 default="text"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 description="Person's password",
     *                 type="string",
     *                 default="text"
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

        $input = $this->getElementsFromRequest($request);

        // If a validator is setted, check if input is validate
        if (!$this->validator || $this->validator->validate($input)) {
            $input['password'] = Hash::make($input['password']);
            $userData = $this->getRepository()->create($input);

            Mail::to($input['email'])->send(new Confirmation($input));

            return response()->json($this->getResource()::make($userData), 201);
        }
    }

    /**
     * @OA\Patch(
     *     path="/adminUsers/{userId}",
     *     tags={"CRUD\AdminUser"},
     *     summary="Update AdminUser by ID",
     *     description="Update a single AdminUser.",
     *     operationId="updateUser",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID of AdminUser that to be updated",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation.",
     *         @OA\JsonContent(ref="#/components/schemas/AdminUser")
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
     *         description="Updated AdminUser object",
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="name",
     *                 description="Person's name",
     *                 type="string",
     *                 default="text"
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 description="Person's email",
     *                 type="string",
     *                 default="text"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 description="Person's password",
     *                 type="string",
     *                 default="text"
     *             )
     *         )
     *     )
     * )
     * @param Request $request
     * @param $itemId
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthenticationException
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
     *     path="/adminUsers/{userId}",
     *     tags={"CRUD\AdminUser"},
     *     summary="Delete AdminUser by ID.",
     *     description="Delete a single AdminUser.",
     *     operationId="deleteAdminUserById",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID of AdminUser that needs to be deleted",
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

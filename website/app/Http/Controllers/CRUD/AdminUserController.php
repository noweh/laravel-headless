<?php

namespace App\Http\Controllers\CRUD;

use App\Exceptions\AuthenticationException;
use App\Repositories\AdminUserRepository;
use App\Services\Validators\AdminUserValidator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Exception;
use Log;
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
     * @return array
     * @throws \App\Exceptions\AuthenticationException
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function getScopeQueries(): array
    {
        $scopeQueries = parent::getScopeQueries();

        $authenticatedUser = $this->getAuthenticatedUser();
        if (!$authenticatedUser->is_superadmin) {
            // If user is not a superadmin, force client_id by its own
            $scopeQueries += ['admin_user_id' => $authenticatedUser->admin_user_id];
        }

        return $scopeQueries;
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
     *         name="removeCache",
     *         required=false,
     *         in="query",
     *         description="Display without cache",
     *         @OA\Schema(
     *             type="boolean"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="include",
     *         required=false,
     *         in="query",
     *         description="Include relationship in results. String=client",
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
     */

    /**
     * @OA\Get(
     *     path="/adminUsers/{userId}",
     *     tags={"CRUD\AdminUser"},
     *     summary="Find a AdminUser by ID.",
     *     description="Returns a single AdminUser.",
     *     operationId="getAdminUserById",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="removeCache",
     *         required=false,
     *         in="query",
     *         description="Display without cache",
     *         @OA\Schema(
     *             type="boolean"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="include",
     *         required=false,
     *         in="query",
     *         description="Include relationship in results. String=client",
     *         @OA\Schema(
     *             type="string"
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
     */

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
     *         description="Create an AdminUser object",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                @OA\Property(
     *                     property="first_name",
     *                     description="Person's first_name",
     *                     type="string",
     *                     default="John"
     *                 ),
     *                 @OA\Property(
     *                     property="last_name",
     *                     description="Person's last_name",
     *                     type="string",
     *                     default="Doe"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     description="Person's email",
     *                     type="string",
     *                     default="john.doe@mazarine.com"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="Person's password",
     *                     type="string",
     *                     default="Passw0rd!"
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     description="Person's password confirmation",
     *                     type="string",
     *                     default="Passw0rd!"
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
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        // Check if AdminUser is authenticated
        $this->getSuperadminUser();

        $input = $this->updateInputBeforeSave($this->getElementsFromRequest($request));

        // If a validator is setted, check if input is validate
        if (!$this->validator || $this->validator->validate($input)) {
            $userData = $this->getRepository()->create($input);

            try {
                Mail::to($input['email'])->send(new Confirmation($input));
            } catch (Exception $e) {
                Log::error(
                    "Send Account creation confirmation mail error",
                    [
                        'method' => __METHOD__,
                        'email' => $input['email']
                    ]
                );
            }

            return response()->json(['data' => $this->getResource()::make($userData)], 201);
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
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="first_name",
     *                     description="Person's first_name",
     *                     type="string",
     *                     default="John"
     *                 ),
     *                 @OA\Property(
     *                     property="last_name",
     *                     description="Person's last_name",
     *                     type="string",
     *                     default="Doe"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     description="Person's email",
     *                     type="string",
     *                     default="john.doe@mazarine.com"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="Person's password",
     *                     type="string",
     *                     default="Passw0rd!"
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     description="Person's password confirmation",
     *                     type="string",
     *                     default="Passw0rd!"
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
    public function update(Request $request): \Illuminate\Http\JsonResponse
    {
        $route = \Route::current();

        // Check if adminUser can update this client
        $authenticatedUser = $this->getAuthenticatedUser();
        if (!$authenticatedUser->is_superadmin && $authenticatedUser->admin_user_id !== last($route->parameters())) {
            throw new AuthenticationException(null, 'Insufficient rights');
        }

        return parent::update($request);
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
    public function destroy($itemId): \Illuminate\Http\JsonResponse
    {
        // Check if adminUser is authenticated
        $this->getSuperadminUser();

        return parent::destroy($itemId);
    }

    /**
     * Override doUpdateObject
     * @param Request $request
     * @param $itemId
     * @return Model|\stdClass|array Which is normally a Model.|null
     * @throws \App\Exceptions\ValidationException
     */
    protected function doUpdateObject(Request $request, $itemId)
    {
        $dataElementsCandidate = $this->getResource()::make(
            $this->getRepository()->getById($itemId, $this->parseIncludes())
        )->toArray([]);
        if ($dataElementsCandidate instanceof \stdClass) {
            $dataElementsCandidate = (array)$dataElementsCandidate;
        }
        $existingData = $this->getElementsFromData($dataElementsCandidate);

        $input = $this->updateInputBeforeSave(
            $this->getElementsFromRequest($request),
            $existingData
        );

        // Override validator to remove email verification to own account
        $this->validator->rules['email'] = 'required|string|email|max:255|' .
            \Illuminate\Validation\Rule::unique('admin_users')->whereNot('admin_user_id', $itemId);

        // If a validator is setted, check if existingData + input are validate
        if (!$this->validator || $this->validator->validate(array_merge($existingData, $input))) {
            $this->getRepository()->update($itemId, $input);
            return $this->getRepository()->getById($itemId);
        }

        return null;
    }
}

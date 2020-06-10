<?php

namespace App\Http\Controllers\Auth\Admin;

use App\Http\Controllers\Auth\AbstractAuthController;
use App\Http\Resources\AdminUserResource;
use Auth;
use App\Repositories\AdminUserRepository;

class AuthController extends AbstractAuthController
{
    public function __construct(AdminUserRepository $repository)
    {
        Auth::shouldUse('api');
        $this->repository = $repository;
        $this->claim = 'admin';
        $this->setResource(AdminUserResource::class);
    }

    /**
     * @OA\Post(
     *     path="/admin/auth/login",
     *     tags={"Admin\Authentication"},
     *     summary="Authenticate an admin user",
     *     operationId="login",
     *     @OA\Response(
     *         response=200,
     *         description="Authenticated."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Login failed."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error."
     *     ),
     *     @OA\RequestBody(
     *         description="Credentials",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="email",
     *                     description="Person's email",
     *                     type="string",
     *                     default="julien.schmitt@mazarinedigital.com",
     *                     example="julien.schmitt@mazarinedigital.com"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="Person's password",
     *                     type="string",
     *                     default="julien.schmitt",
     *                     example="julien.schmitt"
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    /**
     * @OA\Get(
     *     path="/admin/auth/logout",
     *     tags={"Admin\Authentication"},
     *     summary="Deauthenticate a user",
     *     operationId="logout",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout done.",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Logout failed."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error."
     *     )
     * )
     */

    /**
     * @OA\Get(
     *     path="/admin/auth/user",
     *     tags={"Admin\Authentication"},
     *     summary="Get information about connected user",
     *     operationId="auth_user_info",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="include",
     *         required=false,
     *         in="query",
     *         description="Include relationship in results. String=client",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Show user infos.",
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
     */

    /**
     * Refresh a token.
     *
     * @OA\Get(
     *     path="/admin/auth/refresh",
     *     tags={"Admin\Authentication"},
     *     summary="Refresh a token",
     *     operationId="refresh",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="New token.",
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
     */

    /**
     * Test a token.
     *
     * @OA\Get(
     *     path="/admin/auth/test",
     *     tags={"Admin\Authentication"},
     *     summary="Test a token",
     *     operationId="test",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token verified.",
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
     */

    /**
     * Test and refresh a token.
     *
     * @OA\Get(
     *     path="/admin/auth/testAndRefresh",
     *     tags={"Admin\Authentication"},
     *     summary="Test and refresh a token",
     *     operationId="testAndRefresh",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Status and New token.",
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
     */
}

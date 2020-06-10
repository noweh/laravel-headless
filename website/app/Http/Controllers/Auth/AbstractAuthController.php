<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\AuthenticationException;
use App\Http\Controllers\AbstractController;
use App\Http\Resources\AdminUserResource;
use Exception;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon\Carbon;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

abstract class AbstractAuthController extends AbstractController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthenticationException
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                throw new Exception();
            }
        } catch (Exception $e) {
            throw new AuthenticationException($e->getMessage(), 'Could not create token (Invalid Email or Password ('.$e->getMessage().')');
        }
        return $this->respondWithToken($token);
    }

    /**
     * De-authenticate a user
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthenticationException
     */
    public function logout()
    {
        try {
            if (JWTAuth::parseToken()->getClaim('origin') == $this->claim) {
                JWTAuth::invalidate(JWTAuth::getToken());

                return response()->json(['data' => ['message' => 'User logged out successfully']]);
            } else {
                throw new AuthenticationException(null, 'Invalid Token');
            }
        } catch (JWTException $e) {
            throw new AuthenticationException($e->getMessage(), 'The user cannot be logged out');
        }
    }

    /**
     * Get information about connected user
     * @return AdminUserResource
     * @throws AuthenticationException
     * @throws JWTException
     */
    public function show()
    {
        $authenticatedUser = $this->getAuthenticatedUser();

        return $this->getResource()::make(
            $this->getRepository()->getById($authenticatedUser->getJWTIdentifier(), $this->parseIncludes())
        );
    }

    /**
     * Refresh a token
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthenticationException
     */
    public function refresh()
    {
        try {
            if (JWTAuth::parseToken()->getClaim('origin') == $this->claim) {
                return $this->respondWithToken(JWTAuth::parseToken()->refresh());
            } else {
                throw new AuthenticationException(null, 'Invalid Token');
            }
        } catch (JWTException $e) {
            throw new AuthenticationException($e->getMessage(), 'The token cannot be refreshed');
        }
    }

    /**
     * Test if given token is still valid
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthenticationException
     * @throws JWTException
     */
    public function test()
    {
        $this->getAuthenticatedUser();
        return response()->json(['data' => ['status' => 'OK']], 200);
    }

    /**
     * Test if given token is still valid and refresh if needed
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthenticationException
     */
    public function testAndRefresh()
    {
        try {
            if (JWTAuth::parseToken()->getClaim('origin') == $this->claim) {
                $payload = JWTAuth::parseToken()->getPayload()->get('exp');

                switch (true) {
                    case ($payload - Carbon::now()->timestamp > 600):
                        return response()->json(['data' => ['status' => 1]], 200);
                        break;
                    case ($payload - Carbon::now()->timestamp > 0):
                        return response()->json([
                            'data' => [
                                'status' => 2,
                                'access_token' => JWTAuth::parseToken()->refresh(),
                                'token_type' => 'bearer',
                                'expires_in' => JWTAuth::factory()->getTTL()
                            ]
                        ], 200);
                        break;
                }
            } else {
                throw new AuthenticationException(null, 'Invalid Token');
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['data' => ['status' => 0]], 200);
        } catch (JWTException $e) {
            throw new AuthenticationException($e->getMessage(), 'The token cannot be tested');
        }

        return response()->json(['data' => ['status' => 0]], 200);
    }

    /**
     * Get the token array structure.
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json(['data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL()
            ]
        ]);
    }
}

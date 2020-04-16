<?php

namespace App\Http\Middleware;

use App\Exceptions\AuthenticationException;
use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class VerifyJwtFrontstageToken extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws AuthenticationException
     */
    public function handle($request, Closure $next)
    {
        if ($request->method() == 'POST' || $request->method() == 'PATCH' || $request->method() == 'DELETE') {
            try {
                $jwtObject = JWTAuth::parseToken();
                $authenticatedUser = $jwtObject->authenticate();
            } catch (TokenInvalidException $tie) {
                throw new AuthenticationException(null, 'Invalid Token');
            } catch (TokenExpiredException $e) {
                throw new AuthenticationException(null, 'Token expired');
            } catch (JWTException $e) {
                throw new AuthenticationException(null, 'Authorization Token not found');
            }

            if ('frontstage' != $jwtObject->getClaim('origin')) {
                throw new AuthenticationException(null, 'Access forbidden');
            }

            if (is_bool($authenticatedUser)) {
                throw new AuthenticationException(null, 'User not found');
            }
        }

        return $next($request);
    }
}

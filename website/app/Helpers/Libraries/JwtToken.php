<?php

namespace App\Helpers\Libraries;

use ReallySimpleJWT\Token;
use ReallySimpleJWT\Build;
use ReallySimpleJWT\Encode;
use ReallySimpleJWT\Exception\ValidateException;
use Config;

class JwtToken extends Token
{
    public static function getToken($userId, $originClaim, $expires = null)
    {
        if (null === $expires) {
            $expires = (int)Config::get('jwt.frontend_ttl', 360) * 60;
        }

        $payload = [
            'iss' => config('app.url'),
            'sub' => $userId,
            'aud' => '*',
            'exp' => time() + $expires,
            'nbf' => time(),
            'iat' => time(),
            'jti' => uniqid(),
            'origin' => $originClaim,
        ];
        $token = self::customPayload($payload, config('jwt.secret'));
        return $token;
    }

    /**
     * Create a JSON Web Token with a custom payload built from a key
     * value array.
     *
     * @param array $payload
     *
     * @return string
     * @throws ValidateException
     */
    public static function customPayload(array $payload, string $secret): string
    {
        $builder = self::builder();

        foreach ($payload as $key => $value) {
            if (is_int($key)) {
                throw new ValidateException('Invalid payload claim.', 8);
            }

            $builder->setPayloadClaim($key, $value);
        }

        return $builder->setSecret($secret)
            ->build()
            ->getToken();
    }

    /**
     * Factory method to return an instance of the ReallySimpleJWT\Build class.
     *
     * @return Build
     */
    public static function builder(): Build
    {
        return new Build('JWT', new JwtTokenLaxValidate(), new Encode());
    }
}
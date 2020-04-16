<?php

namespace App\Rules;

use App\Exceptions\AuthenticationException;
use Illuminate\Contracts\Validation\Rule;
use App\Exceptions\ValidationException;
use App\Exceptions\SoapException;
use App\Repositories\UserRepository;
use App\Helpers\Secutix\SoapSecutix;
use JWTAuth;
use Request;
use App;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class UniqueEmail implements Rule
{

    protected $userRepository;
    protected $soapSecutix;

    /**
     * Create a new rule instance.
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->soapSecutix = new SoapSecutix('contact');
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     * @throws ValidationException
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function passes($attribute, $value)
    {
       $ret = false;

        if ('POST' === Request::getRealMethod()) {
            if (! ($candidate = $this->userRepository->getFirstBy('email', $value))) {
                try {
                    $searchResponse = $this->soapSecutix->searchUserByCriteria('EMAIL', $value, 0, 5);
                } catch(SoapException $se) {
                    $ret = true;
                }
            }
        } elseif ('PATCH' === Request::getRealMethod()) {
            try {
                $jwtObject = JWTAuth::parseToken();
            } catch (TokenInvalidException $tie) {
                throw new AuthenticationException(null, 'Invalid Token');
            }
            if ($authenticatedUser = $jwtObject->authenticate()) {
                if($value != $authenticatedUser->email) {
                    $ret = $this->userRepository->findAlreadyUsedEmail($authenticatedUser, $value);
                } else {
                    $ret = true;
                }
            }
        } else {
            die('Unexpected HTTP method');
        }

        return $ret;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if ('fr' == App::getLocale()) {
            return 'Le adresse "e-mail" invalide ou déjà utilisée.';
        } else {
            return 'Invalid or already used "e-mail" address.';
        }
    }
}

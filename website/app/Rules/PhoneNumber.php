<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Services\Application\Gamification\PhoneNumberService;
use App\Exceptions\ValidationException;
use Config;
use Request;
use App;

class PhoneNumber implements Rule
{
    
    protected $phoneNumberService;

    /**
     * Create a new rule instance.
     *
     * @param PhoneNumberService $phoneNumberService
     */
    public function __construct(PhoneNumberService $phoneNumberService)
    {
        $this->phoneNumberService = $phoneNumberService;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     * @throws ValidationException
     */
    public function passes($attribute, $value)
    {
        $ret = false;
        try {
            $ret = $this->phoneNumberService->validate(
                $value,
                Request::input('phone_number_country_code', null)
            );
        } catch (Exception $e) {
            throw new ValidationException($e->getMessage(), 'Error while validating phone number.');
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
            return 'Le format du champ téléphone est invalide.';
        } else {
            return 'The phone number field has an incorrect format.';
        }
    }
}

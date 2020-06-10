<?php

namespace App\Services\Validators;

use Illuminate\Validation\Factory as IlluminateValidator;
use App\Exceptions\ValidationException;
use Config;
use App;

/**
 * Base Validation class. All entity specific validation classes inherit
 * this class and can override any function for respective specific needs
 */
abstract class AbstractValidator
{
    /**
     * @var IlluminateValidator
     */
    protected $validator;

    /**
     * AbstractValidator constructor.
     * @param IlluminateValidator $validator
     */
    public function __construct(IlluminateValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param array $data
     * @param array $rules
     * @return bool
     * @throws ValidationException
     */
    public function validate(array $data, array $rules = [])
    {
        if (empty($rules) && !empty($this->rules) && is_array($this->rules)) {
            //no rules passed to function, use the default rules defined in sub-class
            $rules = $this->rules;
        }

        if (!empty($this->translatedFieldsRules) && is_array($this->translatedFieldsRules)) {
            // If it's a creation : all locales are checked, else, only current locale
            $languages = (isset($data['id'])) && $data['id'] ?
                [App::getLocale()] : array_keys(Config::get('app.locales', []))
            ;

            foreach ($languages as $locale) {
                //$rules["active_{$locale}"] = "required|boolean";
                if ($locale == 'fr' || !empty($data["active_{$locale}"]) && $data["active_{$locale}"]) {
                    foreach ($this->translatedFieldsRules as $field => $field_rules) {
                        $rules["{$field}_{$locale}"] = $field_rules;
                    }
                }
            }
        }

        //use Laravel's Validator and validate the data
        $validation = $this->validator->make($data, $rules);

        if ($validation->fails()) {
            //validation failed, throw an exception
            throw new ValidationException(
                $validation->messages(),
                json_encode($validation->messages()->getMessages())
            );
        }

        return true;
    }
}

<?php

namespace App\Services\Validators;

use Illuminate\Validation\Factory as IlluminateValidator;
use App\Services\Application\Gamification\PhoneNumberService;
use App\Exceptions\ValidationException;
use App\Rules\PhoneNumber;
use App\Rules\UniqueEmail;
use App\Models\User;
use App\Repositories\UserRepository;
use Config;
use App;
use Request;

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

        $rules['published'] = 'boolean';
        if (isset($data['published']) && $data['published'] == true) {
            if (!empty($this->translatedFieldsRules) && is_array($this->translatedFieldsRules)) {
                // If it's a creation : all locales are checked, else, only current locale
                $languages = (isset($data['id'])) && $data['id'] ?
                    [App::getLocale()] : array_keys(Config::get('app.locales', []))
                ;

                // If it's a creation : check association_id setted, else, check association setted
                foreach ($rules as $field => $rule) {
                    if (Request::method() == 'POST') {
                        if (in_array($field, ['themes', 'tags', 'questions', 'cards'])) {
                            $rules[$field . '_id'] = 'required';
                            unset($rules[$field]);
                        }
                    }
                }

                foreach ($languages as $locale) {
                    //$rules["active_{$locale}"] = "required|boolean";
                    if ($locale == 'fr' || !empty($data["active_{$locale}"]) && $data["active_{$locale}"]) {
                        foreach ($this->translatedFieldsRules as $field => $field_rules) {
                            $rules["{$field}_{$locale}"] = $field_rules;
                        }
                    }
                }
            }

            if (isset($rules['type'])) {
                $path = explode('\\', get_class($this));
                $model = 'App\Models\\' . str_replace('Validator', '', array_pop($path));
                if ($model::TYPES) {
                    $rules['type'] = $rules['type'] . '|in:' . implode(',', $model::TYPES);
                }
            }

            if (isset($rules['orientation'])) {
                $path = explode('\\', get_class($this));
                $model = 'App\Models\\' . str_replace('Validator', '', array_pop($path));
                $rules['orientation'] = $rules['orientation'] . '|in:' . implode(',', $model::orientationDefinition());
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
        }

        return true;
    }

    /**
     * @param array $data
     * @param array $rules
     * @return bool
     * @throws ValidationException
     */
    public function validateUserData(array $data, array $rules = [])
    {
        if (empty($rules) && !empty($this->rules) && is_array($this->rules)) {
            //no rules passed to function, use the default rules defined in sub-class
            $rules = $this->rules;
        }
        
        $postCheck = [];
        foreach ($rules as $k => $v) {
            if ('lang' == $k) {
                $rules[$k] = trim($v, '|') . '|in:' . join(',', array_keys(Config::get('app.locales', [])));
            } elseif ('phone_number' == $k) {
                $postCheck[$k] = new PhoneNumber(new PhoneNumberService());
            } elseif ('unique_email' == $k) {
                $postCheck[$k] = new UniqueEmail(new UserRepository(new User()));
            }
        }
        
        //use Laravel's Validator and validate the data
        $validation = $this->validator->make($data, $rules);

        if ($validation->fails()) {
            //validation failed, throw an exception
            $errMsg = [];
            foreach ($validation->messages()->getMessages() as $k => $v) {
                $errMsg[] = '[' . $k . '] ' . join(', ', $v);
            }
            throw new ValidationException(
                $validation->messages(),
                join(' | ', $errMsg)
            );
        } elseif (0 < count($postCheck)) {
            $rules = [];
            foreach ($postCheck as $field => $check) {
                $rules[$field] = $check;
            }
            $validation = $this->validator->make($data, $rules);

            if ($validation->fails()) {
                $errMsg = [];
                foreach ($validation->messages()->getMessages() as $k => $v) {
                    $errMsg[] = '[' . $k . '] ' . join(', ', $v);
                }
                throw new ValidationException(
                    $validation->messages(),
                    join(' | ', $errMsg)
                );
            }
        }
        
        return true;
    }
}

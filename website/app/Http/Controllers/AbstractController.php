<?php

namespace App\Http\Controllers;

use App\Exceptions\AuthenticationException;
use App\Exceptions\ValidationException;
use App\Models\AdminUser;
use Auth;
use Carbon\Carbon;
use Config;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App;
use Str;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

/**
 * Class AbstractController
 * @package App\Http\Controllers
 *
 * @OA\Info(
 *     version="1.0",
 *     title="API",
 * )
 *
 * @OA\Server(
 *     description="API Local",
 *     url=L5_SWAGGER_CONST_HOST
 * )
 *
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      in="header",
 *      name="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 * ),
 *
 * @OA\Tag(
 *     name="Admin\Authentication",
 *     description="Operations about admin users Authentication"
 * )
 *
 * @OA\Tag(
 *     name="CRUD\AdminUser",
 *     description="Operations about AdminUsers"
 * )
 *
 * @OA\Tag(
 *     name="CRUD\MediaLibrary",
 *     description="Operations about MediaLibraries"
 * )
 *
 * @OA\Tag(
 *     name="CRUD\Show",
 *     description="Operations about Shows"
 * )
 *
 * @OA\Tag(
 *     name="Setting",
 *     description="Operations about Settings"
 * )
 */
abstract class AbstractController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $resource;
    protected $repository;
    protected $request;
    protected $validator;
    protected $page = 1;
    protected $nbPerPage = 25;
    protected $slug;
    protected $id;
    protected $claim = 'frontend';
    protected $authenticatedUser;

    /**
     * AbstractController constructor.
     */
    public function __construct()
    {
        if (request('page') && is_numeric(request('page'))) {
            $this->page = request('page');
        }

        if (request('per_page') && is_numeric(request('per_page'))) {
            $this->nbPerPage = request('per_page');
        }

        if (request('slug')) {
            $this->slug = request('slug');
        }
    }

    /**
     * Set the Controller's Resource path
     * @param $resource
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
    }

    /**
     * Get the Controller's Resource path
     * @return string
     * @throws ModelNotFoundException
     */
    public function getResource()
    {
        if (!$this->resource) {
            $path = explode('\\', get_class($this));
            $this->resource = 'App\Http\Resources\\' . str_replace('Controller', 'Resource', array_pop($path));
        }

        if (!class_exists($this->resource)) {
            throw new ModelNotFoundException('Resource ' . $this->resource . ' not found');
        }

        return $this->resource;
    }

    /**
     * @return mixed
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return array
     */
    protected function getScopeFilters(): array
    {
        $scope = [];

        // Retrieve all filters for associated data
        foreach (request()->all() as $parameter => $values) {
            if (substr($parameter, 0, 1) === 'f' && strpos($parameter, '_') !== false) {
                $scope += $this->getFilterValues(
                    lcfirst(substr(str_replace('_', '.', $parameter), 1)),
                    explode(',', $values)
                );
            }
        }

        // Retrieve all filters for current model
        $model = $this->getRepository()->getModel();
        $filters = array_merge($model->getFillable(), $model->getTranslatedAttributes());

        foreach ($filters as $field) {
            if (request('f' . ucfirst(Str::camel($field)))) {
                $scope += $this->getFilterValues(
                    $field,
                    explode(',', request('f' . ucfirst(Str::camel($field))))
                );
            }
        }

        return $scope;
    }

    /**
     * @return array
     */
    protected function getScopeQueries(): array
    {
        $scope = [];

        // Retrieve all filters for associated data
        foreach (request()->all() as $parameter => $values) {
            if ($parameter[0] === 'q' && strpos($parameter, '_') !== false) {
                $scope += $this->getFilterValues(
                    lcfirst(substr(str_replace('_', '.', $parameter), 1)),
                    explode(',', $values)
                );
            }
        }

        // Retrieve all filters for current model
        $model = $this->getRepository()->getModel();
        $filters = array_merge($model->getFillable(), $model->getTranslatedAttributes());

        foreach ($filters as $field) {
            if (request('q' . ucfirst(Str::camel($field)))) {
                $scope += $this->getFilterValues(
                    $field,
                    explode(',', request('q' . ucfirst(Str::camel($field))))
                );
            }
        }

        return $scope;
    }

    /**
     * @param $field
     * @param $values
     * @return array
     */
    private function getFilterValues($field, $values): array
    {
        $scope = [];

        if (count($values) > 1) {
            foreach ($values as $key => $value) {
                if ($value == null) {
                    unset($values[$key]);
                }
            }
        }

        foreach ($values as $value) {
            if ($value == 'true') {
                $value = 1;
            }
            if ($value == 'false') {
                $value = 0;
            }
            if ($value == 0 || !empty($value)) {
                if (count($values) > 1) {
                    $scope[$field][] = $value;
                } else {
                    $scope[$field] = $value;
                }
            }
        }

        return $scope;
    }

    /**
     * @return array
     */
    protected function getOrderFilters(): array
    {
        $orders = [];
        if (request('sort')) {
            $sortKey = request('sort');
            $orders[
                substr($sortKey, -3, 1) == '_' &&
                in_array(substr($sortKey, -2), array_keys(Config::get('app.locales', []))) ?
                substr($sortKey, 0, -3) : $sortKey
            ] = strtolower(request('sortOrder')) == 'desc' ? 'desc' : 'asc';
        }

        return $orders;
    }

    /**
     * @return array
     */
    protected function getRelationshipOrderByQuantityFilters(): array
    {
        $relationshipOrdersByQuantity = [];
        if (request('relSortByQuantity')) {
            $relationshipOrdersByQuantity[request('relSortByQuantity')] =
                strtolower(request('sortOrder')) == 'desc' ? 'desc' : 'asc';
        }
        return $relationshipOrdersByQuantity;
    }

    /**
     * @return array
     */
    protected function getExcludeIdsFilters(): array
    {
        $excludeIds = [];
        if (request('excludeIds')) {
            $excludeIds = explode(',', request('excludeIds'));
        }

        return $excludeIds;
    }

    /**
     * Get all includes to set for the Model Collection
     * @return array
     */
    protected function parseIncludes(): array
    {
        return request('include') ? explode(',', request('include')) : [];
    }

    /**
     * Retrieve all elements from Request and set translatedElements
     * @param Request $request
     * @return array
     */
    protected function getElementsFromRequest(Request $request): array
    {
        return $this->getElementsFromData($request->request->all());
    }

    /**
     * can be used in override, to add some verifications or rules on input
     * @param array $input
     * @param array|null $existingData
     * @return array
     * @throws ValidationException
     */
    protected function updateInputBeforeSave(array $input, array $existingData = null): array
    {
        if (empty($input)) {
            throw new ValidationException(null, 'No data in input or invalid format for data');
        }

        if ($input) {
            // set updated_at with a value to force base model to update
            $input['updated_at'] = Carbon::now();
        }

        return $input;
    }

    /**
     * Retrieve all elements from array and set translatedElements
     * @param array $data
     * @return array
     */
    protected function getElementsFromData(array $data): array
    {
        $reconstructedElements = [];
        foreach ($data as $elementKeyInRequest => $elementValueInRequest) {
            if (in_array($elementKeyInRequest, $this->getRepository()->getModel()->getTranslatedAttributes())) {
                $reconstructedElements[$elementKeyInRequest . '_' . App::getLocale()] = $elementValueInRequest;
            } else {
                $reconstructedElements[$elementKeyInRequest] = $elementValueInRequest;
            }
        }

        return $reconstructedElements;
    }

    /**
     * Check if given token authenticate a user.
     * @return AdminUser|\Tymon\JWTAuth\Contracts\JWTSubject
     * @throws AuthenticationException
     * @throws JWTException
     */
    public function getAuthenticatedUser()
    {
        if (!$this->authenticatedUser) {
            try {
                $jwtObject = JWTAuth::parseToken();
                $this->authenticatedUser = $jwtObject->authenticate();
            } catch (TokenInvalidException $tie) {
                throw new AuthenticationException(null, 'Invalid Token');
            } catch (TokenExpiredException $e) {
                throw new AuthenticationException(null, 'Token expired');
            } catch (JWTException $e) {
                throw new AuthenticationException(null, 'Authorization Token not found');
            }

            if (JWTAuth::parseToken()->getClaim('origin') != $this->claim) {
                throw new AuthenticationException(null, 'Invalid Token');
            }

            if (is_bool($this->authenticatedUser)) {
                throw new AuthenticationException(null, 'User not found ('. $jwtObject->getPayload()->get('sub') .')');
            }
        }


        return $this->authenticatedUser;
    }

    /**
     * @return mixed
     * @throws AuthenticationException
     * @throws JWTException
     */
    public function getSuperadminUser()
    {
        if (!$this->getAuthenticatedUser()->is_superadmin) {
            throw new AuthenticationException(null, 'Insufficient rights');
        }

        return $this->authenticatedUser;
    }
}

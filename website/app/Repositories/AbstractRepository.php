<?php

namespace App\Repositories;

use Carbon\Carbon;
use DB;
use Hash;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App;
use Config;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Schema;
use Cache;

abstract class AbstractRepository
{
    /**
     * @var Model|null
     */
    protected $model = null;

    /**
     * Fields listed in that array must be nullable
     *
     * @var array list of field name
     */
    protected $forceSetTranslateFieldBeforeSave = [];

    /**
     * @return Model|null
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Apply scopes filters and orders sorts to a query.
     * It return the modified query for chaining.
     *
     * @param Builder $query
     * @param array $scopes list of scope the entity need to match [field => value,...]
     * @param array $queries list of queries the entity need to match [field => value,...]
     * @param array $orders list of field to sort on ['field1' => 'asc', 'field2' => 'desc',...]
     * @param array $excludeIds list of ids to exclude
     * @return Builder  list of field to sort on ['field1' => 'asc', 'field2' => 'desc',...]
     */
    public function setScopesAndOrders(
        $query,
        array $scopes = [],
        array $queries = [],
        array $orders = [],
        array $excludeIds = []
    ) {
        // Filter data for associated models
        $modelScopes = [];
        foreach ($scopes as $key => $value) {
            if (strpos($key, '.') !== false) {
                $keys = explode('.', $key);

                $array = $value;
                foreach (array_reverse($keys) as $_key) {
                    $array = [$_key => $array];
                }
                // Group filters by same parent
                $modelScopes = array_merge_recursive($modelScopes, $array);

                unset($scopes[$key]);
            }
        }

        $modelQueries = [];
        foreach ($queries as $key => $value) {
            if (strpos($key, '.') !== false) {
                $keys = explode('.', $key);

                $array = $value;
                foreach (array_reverse($keys) as $_key) {
                    $array = [$_key => $array];
                }
                // Group filters by same parent
                $modelQueries = array_merge_recursive($modelQueries, $array);

                unset($queries[$key]);
            }
        }

        if ($modelScopes) {
            foreach ($modelScopes as $modelName => $values) {
                $model = null;
                if (class_exists('App\Models\\' . ucfirst($modelName))) {
                    $modelRoute = 'App\Models\\' . ucfirst($modelName);
                    $model = new $modelRoute();
                } elseif (class_exists('App\Models\\' .ucfirst(mb_substr($modelName, 0, -1)))) {
                    $modelRoute = 'App\Models\\' . ucfirst(mb_substr($modelName, 0, -1));
                    $model = new $modelRoute();
                }

                if (!$model) {
                    throw new ModelNotFoundException(
                        'Model App\Models\\' . ucfirst($modelName) .
                        ' or App\Models\\' . mb_substr($modelName, 0, -1) . ' not found'
                    );
                }

                $query->whereHas($modelName, function ($q) use ($model, $values) {
                    $this->setTranslateScope($q, $values, $model);

                    foreach ($values as $scopeKey => $scopeValue) {
                        if (in_array($scopeKey, $model->getFillable())) {
                            $this->filterData($q, $scopeKey, $scopeValue);
                        } else {
                            throw new ModelNotFoundException(
                                'Method ' . $scopeKey . ' not found in ' . get_class($model)
                            );
                        }
                    }
                });
            }
        }

        if ($modelQueries) {
            foreach ($modelQueries as $modelName => $values) {
                $model = null;
                if (class_exists('App\Models\\' . ucfirst($modelName))) {
                    $modelRoute = 'App\Models\\' . ucfirst($modelName);
                    $model = new $modelRoute();
                } elseif (class_exists('App\Models\\' .ucfirst(mb_substr($modelName, 0, -1)))) {
                    $modelRoute = 'App\Models\\' . ucfirst(mb_substr($modelName, 0, -1));
                    $model = new $modelRoute();
                }

                if (!$model) {
                    throw new ModelNotFoundException(
                        'Model App\Models\\' . ucfirst($modelName) .
                        ' or App\Models\\' . mb_substr($modelName, 0, -1) . ' not found'
                    );
                }

                $query->whereHas($modelName, function ($q) use ($model, $values) {
                    $this->setTranslateScope($q, $values, $model);

                    foreach ($values as $scopeKey => $scopeValue) {
                        if (in_array($scopeKey, $model->getFillable())) {
                            $this->queryData($q, $scopeKey, $scopeValue);
                        } else {
                            throw new ModelNotFoundException(
                                'Method ' . $scopeKey . ' not found in ' . get_class($model)
                            );
                        }
                    }
                });
            }
        }

        // Filter data for current model
        $this->setTranslateScope($query, $scopes);
        $this->setTranslateQuery($query, $queries);

        foreach ($scopes as $key => $value) {
            $this->filterData($query, $key, $value);
        }

        foreach ($queries as $key => $value) {
            $this->queryData($query, $key, $value);
        }

        if ($excludeIds) {
            $query->whereNotIn($this->model->getTable() . '.id', $excludeIds);
        }

        $this->setTranslateOrder($query, $orders);
        foreach ($orders as $key => $value) {
            $query->orderBy($key, $value);
        }

        return $query;
    }

    /**
     * Filter data on query, if text contains value
     *
     * @param Builder $query
     * @param string $key
     * @param string|array $value
     * @return Builder
     */
    private function filterData($query, $key, $value)
    {
        if (is_array($value)) {
            $query->where(function ($query2) use ($key, $value) {
                foreach ($value as $item) {
                    $query2->orWhere($key, 'LIKE', '%' . $item. '%');
                }
            });
        } elseif ($value == null) {
            $query->where($key, $value);
        } else {
            $query->where($key, 'LIKE', '%' . $value . '%');
        }

        return $query;
    }

    /**
     * Filter data on query, if text is equal to the value
     *
     * @param $query
     * @param $key
     * @param $value
     * @return mixed
     */
    private function queryData($query, $key, $value)
    {
        if (is_array($value)) {
            $query->where(function ($query2) use ($key, $value) {
                foreach ($value as $item) {
                    $query2->orWhere($key, '=', $item);
                }
            });
        } elseif ($value == null) {
            $query->where($key, $value);
        } else {
            $query->where($key, '=', $value);
        }

        return $query;
    }

    /**
     * Alter the query to apply the specified sort order on translated attributes.
     *
     * @param Builder $query
     * @param array $orders
     */
    private function setTranslateOrder($query, &$orders)
    {
        if (property_exists($this->model, 'translatedAttributes')  && !empty($this->model->getTranslatedAttributes())) {
            $attributes = $this->model->getTranslatedAttributes();
            $table = $this->model->getTable();
            $tableTranslation = $this->model->translations()->getRelated()->getTable();

            $foreignKey = $this->model->translations()->getQualifiedForeignKeyName();

            $isOrdered = false;
            foreach ($attributes as $attribute) {
                if (isset($orders[$attribute])) {
                    $query->orderBy($tableTranslation . '.' . $attribute, $orders[$attribute]);
                    $isOrdered = true;
                    unset($orders[$attribute]);
                }
            }

            if ($isOrdered) {
                $query
                    ->join($tableTranslation, $foreignKey, '=', $table.'.id')
                    ->where(
                        $tableTranslation . '.locale',
                        '=',
                        isset($orders['locale']) ? $orders['locale'] : App::getLocale()
                    )
                    ->select($table.'.*')
                ;
            }
        }
    }

    /**
     * Make a new instance of the entity to query on
     *
     * @param array|string $with
     * @return Model|Builder
     */
    protected function make(array $with = [])
    {
        return $this->model->with($with);
    }

    /**
     * Format a name for cache key
     * @param array $params
     * @return string
     */
    protected function getCacheKeyName($params = [])
    {
        $cacheName = Config::get('app.domain_api') . $this->getModel()->getTable();

        foreach ($params as $key => $param) {
            $cacheName .= (is_array($param)) ?
                (!empty($param)) ? '.' . $key . '_' . json_encode($param) : null :
                '.' . $key . '_' . $param;
        }

        return md5($cacheName);
    }

    /**
     * Check if cache can be activated
     * After test, cache can not be activated when an includes ask for a child of a child (data with a ".")
     * @param array $includes
     * @return bool
     */
    protected function checkIfCacheCanBeActivated($includes = [])
    {
        $useCache = true;
        foreach ($includes as $value) {
            if (strpos($value, '.')) {
                // If with contains childs of child, this request must not be cached
                $useCache = false;
            }
        }

        return $useCache;
    }

    /**
     * Fetch a list a list of entities paginated
     *
     * @param integer $take number of item per page
     * @param integer $page number of page
     * @param array $scopes list of scope the entity need to match [field => value,...]
     * @param array $queries list of queries the entity need to match [field => value,...]
     * @param array|string $with list of relations to eager load ['translations', 'artists', 'artists.translations',...]
     * @param array $orders ['fieldname1' => 'asc', 'fieldname2' => 'desc',...]
     * @param array $excludeIds list of Ids to exclude from result
     * @param array $relationshipOrderByQuantity list of relationship to sort on by quantity
     * @return LengthAwarePaginator
     */
    public function getPaginateCollection(
        $take = 5,
        $page = 1,
        $scopes = [],
        $queries = [],
        $with = [],
        $orders = [],
        $excludeIds = [],
        $relationshipOrderByQuantity = []
    ) {
        if (Cache::getDefaultDriver() == 'redis') {
            return Cache::tags('collections')->rememberForever(
                $this->getCacheKeyName([
                    'take' => $take, 'page' => $page, 'scopes' => $scopes, 'queries' => $queries, 'with' => $with,
                    'orders' => $orders, 'excludeIds' => $excludeIds, 'relationships' => $relationshipOrderByQuantity
                ]),
                function () use (
                    $take,
                    $page,
                    $scopes,
                    $queries,
                    $with,
                    $orders,
                    $excludeIds,
                    $relationshipOrderByQuantity
                ) {
                    // If data not in cache, call to eloquent
                    if ($this->checkIfCacheCanBeActivated($with)) {
                        return $this->retrievePaginatedCollectionContent(
                            $take,
                            $page,
                            $scopes,
                            $queries,
                            $with,
                            $orders,
                            $excludeIds,
                            $relationshipOrderByQuantity
                        );
                    } else {
                        return app("model-cache")->runDisabled(
                            function () use (
                                $take,
                                $page,
                                $scopes,
                                $queries,
                                $with,
                                $orders,
                                $excludeIds,
                                $relationshipOrderByQuantity
                            ) {
                                return $this->retrievePaginatedCollectionContent(
                                    $take,
                                    $page,
                                    $scopes,
                                    $queries,
                                    $with,
                                    $orders,
                                    $excludeIds,
                                    $relationshipOrderByQuantity
                                );
                            }
                        );
                    }
                }
            );
        } else {
            if ($this->checkIfCacheCanBeActivated($with)) {
                return $this->retrievePaginatedCollectionContent(
                    $take,
                    $page,
                    $scopes,
                    $queries,
                    $with,
                    $orders,
                    $excludeIds,
                    $relationshipOrderByQuantity
                );
            } else {
                return app("model-cache")->runDisabled(
                    function () use (
                        $take,
                        $page,
                        $scopes,
                        $queries,
                        $with,
                        $orders,
                        $excludeIds,
                        $relationshipOrderByQuantity
                    ) {
                        return $this->retrievePaginatedCollectionContent(
                            $take,
                            $page,
                            $scopes,
                            $queries,
                            $with,
                            $orders,
                            $excludeIds,
                            $relationshipOrderByQuantity
                        );
                    }
                );
            }
        }
    }

    /**
     * Fetch a Collection of entities
     *
     * @param array $scopes list of scope the entity need to match [field => value,...]
     * @param array $queries list of queries the entity need to match [field => value,...]
     * @param array $with list of relations to eager load
     * @param array $orders list of field to sort on ['field1' => 'asc', 'field2' => 'desc',...]
     * @param array $excludeIds list of Ids to exclude from result
     * @param array $relationshipOrderByQuantity list of relationship to sort on by quantity
     * @return Collection
     */
    public function getCollection(
        $scopes = [],
        $queries = [],
        $with = [],
        $orders = [],
        $excludeIds = [],
        $relationshipOrderByQuantity = []
    ) {
        if (Cache::getDefaultDriver() == 'redis') {
            return Cache::tags('collections')->rememberForever(
                $this->getCacheKeyName([
                    'scopes' => $scopes, 'queries' => $queries, 'with' => $with,
                    'orders' => $orders, 'excludeIds' => $excludeIds, 'relationships' => $relationshipOrderByQuantity
                ]),
                function () use (
                    $scopes,
                    $queries,
                    $with,
                    $orders,
                    $excludeIds,
                    $relationshipOrderByQuantity
                ) {
                    // If data not in cache, call to eloquent
                    if ($this->checkIfCacheCanBeActivated($with)) {
                        return $this->retrieveCollectionContent(
                            $scopes,
                            $queries,
                            $with,
                            $orders,
                            $excludeIds,
                            $relationshipOrderByQuantity
                        );
                    } else {
                        return app("model-cache")->runDisabled(
                            function () use (
                                $scopes,
                                $queries,
                                $with,
                                $orders,
                                $excludeIds,
                                $relationshipOrderByQuantity
                            ) {
                                return $this->retrieveCollectionContent(
                                    $scopes,
                                    $queries,
                                    $with,
                                    $orders,
                                    $excludeIds,
                                    $relationshipOrderByQuantity
                                );
                            }
                        );
                    }
                }
            );
        } else {
            if ($this->checkIfCacheCanBeActivated($with)) {
                return $this->retrieveCollectionContent(
                    $scopes,
                    $queries,
                    $with,
                    $orders,
                    $excludeIds,
                    $relationshipOrderByQuantity
                );
            } else {
                return app("model-cache")->runDisabled(
                    function () use (
                        $scopes,
                        $queries,
                        $with,
                        $orders,
                        $excludeIds,
                        $relationshipOrderByQuantity
                    ) {
                        return $this->retrieveCollectionContent(
                            $scopes,
                            $queries,
                            $with,
                            $orders,
                            $excludeIds,
                            $relationshipOrderByQuantity
                        );
                    }
                );
            }
        }
    }

    /**
     * Retrieve Collection Content, called in getCollection and getPaginateCollection
     *
     * @param array $scopes
     * @param array $queries
     * @param array $with
     * @param array $orders
     * @param array $excludeIds
     * @param array $relationshipOrderByQuantity
     * @return Collection
     * @throws RelationNotFoundException
     */
    private function retrieveCollectionContent(
        $scopes = [],
        $queries = [],
        $with = [],
        $orders = [],
        $excludeIds = [],
        $relationshipOrderByQuantity = []
    ) {
        $collection = $this->setScopesAndOrders($this->make($with), $scopes, $queries, $orders, $excludeIds)->get();

        if ($relationshipOrderByQuantity) {
            try {
                $collection = $collection->sortBy(
                    function ($item) use ($relationshipOrderByQuantity) {
                        return $item->{key($relationshipOrderByQuantity)}()->get()->count();
                    },
                    SORT_NATURAL,
                    $relationshipOrderByQuantity[key($relationshipOrderByQuantity)] == 'desc' ? true : false
                );
            } catch (\Exception $e) {
                throw new RelationNotFoundException(
                    'Call to undefined relation \'' . key($relationshipOrderByQuantity) . '\' for model [' . get_class($this->getModel()) . ']'
                );
            }

        }

        return $collection;
    }

    /**
     * Retrieve Paginated Collection Content, called in getPaginateCollection
     *
     * @param int $take
     * @param int $page
     * @param array $scopes
     * @param array $queries
     * @param array $with
     * @param array $orders
     * @param array $excludeIds
     * @param array $relationshipOrderByQuantity
     * @return LengthAwarePaginator
     */
    private function retrievePaginatedCollectionContent(
        $take = 5,
        $page = 1,
        $scopes = [],
        $queries = [],
        $with = [],
        $orders = [],
        $excludeIds = [],
        $relationshipOrderByQuantity = []
    ) {
        $content = $this->retrieveCollectionContent(
            $scopes,
            $queries,
            $with,
            $orders,
            $excludeIds,
            $relationshipOrderByQuantity
        );

        return new LengthAwarePaginator(
            $content->forPage($page, $take),
            $content->count(),
            $take,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => 'page'
            ]
        );
    }

    /**
     * Find an entity by slug
     * @param $slugCandidate
     * @param array $with
     * @throws ModelNotFoundException

     * @return array|Model|\stdClass
     */
    public function getBySlug($slugCandidate, array $with = [])
    {
        $id = null;
        $collection = $this->getCollection()->filter(function ($item) use ($slugCandidate) {
            foreach ($item->getSlugs() as $slug) {
                if ($slug->slug == $slugCandidate) {
                    return $item;
                }
            }
        });
        if ($collection->isEmpty()) {
            $exception = new ModelNotFoundException();
            $exception->setModel(get_class($this->getModel()));
            throw $exception;
        } else {
            return $this->getById(
                $collection->first()->{$collection->first()->getKeyName()},
                $with
            );
        }
    }

    /**
     * Find an entity by id
     *
     * @param int $id
     * @param array|string $with
     * @return Model|\stdClass|array Which is normally a Model.
     */
    public function getById($id, array $with = [])
    {
        if ($this->checkIfCacheCanBeActivated($with)) {
            return $this->make($with)->findOrFail($id);
        } else {
            return app("model-cache")->runDisabled(function () use ($id, $with) {
                return $this->make($with)->findOrFail($id);
            });
        }
    }

    /**
     * Find an entity by id with scope
     *
     * @param int $id
     * @param array $scopes
     * @param array $queries
     * @param array|string $with
     * @return Model|\stdClass|array|null
     */
    public function getByIdWithScope($id, $scopes = [], $queries = [], array $with = [])
    {
        if ($this->checkIfCacheCanBeActivated($with)) {
            return $this->setScopesAndOrders($this->make($with), $scopes, $queries)->findOrFail($id);
        } else {
            return app("model-cache")->runDisabled(function () use ($id, $scopes, $queries, $with) {
                return $this->setScopesAndOrders($this->make($with), $scopes, $queries)->findOrFail($id);
            });
        }
    }

    /**
     * Find a single entity by key value
     *
     * @param string $key
     * @param string $value
     * @param array $with
     * @return Model|mixed
     */
    public function getFirstBy($key, $value, array $with = [])
    {
        if ($this->checkIfCacheCanBeActivated($with)) {
            return $this->make($with)->where($key, '=', $value)->first();
        } else {
            return app("model-cache")->runDisabled(function () use ($key, $value, $with) {
                return $this->make($with)->where($key, '=', $value)->first();
            });
        }
    }

    /**
     * Find a single entity by scope
     * @param array $scopes
     * @param array $with
     * @return array|null|\stdClass
     */
    public function getFirstByScope($scopes = [], array $with = [])
    {
        if ($this->checkIfCacheCanBeActivated($with)) {
            return $this->setScopesAndOrders($this->make($with), $scopes)->first();
        } else {
            return app("model-cache")->runDisabled(function () use ($scopes, $with) {
                return $this->setScopesAndOrders($this->make($with), $scopes)->first();
            });
        }
    }

    /**
     * Find a single entity by key value
     *
     * @param array $with
     * @return \stdClass|array|null
     */
    public function getFirst(array $with = [])
    {
        if ($this->checkIfCacheCanBeActivated($with)) {
            return $this->make($with)->first();
        } else {
            return app("model-cache")->runDisabled(function () use ($with) {
                return $this->make($with)->first();
            });
        }
    }

    /**
     * Find many entities by key value
     *
     * @param string $key
     * @param string $value
     * @param array $with
     *
     * @return Builder[]|Collection
     */
    public function getManyBy($key, $value, array $with = [])
    {
        if ($this->checkIfCacheCanBeActivated($with)) {
            return $this->make($with)->where($key, '=', $value)->get();
        } else {
            return app("model-cache")->runDisabled(function () use ($key, $value, $with) {
                return $this->make($with)->where($key, '=', $value)->get();
            });
        }
    }

    /**
     * [create description]
     * @param  array $fields [description]
     * @return Model         [description]
     */
    public function create($fields)
    {
        if (Cache::getDefaultDriver() == 'redis') {
            // Remove collections cache
            Cache::tags('collections')->flush();
        }

        // Hash passwords
        foreach ($fields as $key => $value) {
            if ($key == 'password') {
                $fields['password'] = Hash::make($value);
            }
        }

        $fields = $this->prepareFieldsBeforeSave($fields);
        $object = $this->model->create($this->prepareDatesFields($fields));

        $this->createTranslations($object, $fields);

        $object->push();
        $this->updateAfter($object, $fields);
        return $object;
    }

    /**
     * [update description]
     * @param  integer $id [description]
     * @param  array $fields [description]
     * @return void [description]
     */
    public function update($id, $fields)
    {
        if (Cache::getDefaultDriver() == 'redis') {
            // Remove collections cache
            Cache::tags('collections')->flush();
        }

        // Set empty field to NULL and Hash passwords
        foreach ($fields as $key => $value) {
            if (empty($value) && $value !== false && $value !== 0) {
                $fields[$key] = null;
            }
            if ($key == 'password') {
                $fields['password'] = Hash::make($value);
            }
        }

        $fields = $this->prepareFieldsBeforeSave($fields);
        $fields = $this->prepareDatesFields($fields);

        $object = $this->model->findOrFail($id);

        $this->updateTranslations($object, $fields);


        // fill others fields
        $object->fill($fields);
        $object->push();
        $this->updateAfter($object, $fields);
    }

    /**
     * Postprocessing of the related info included as traits in the Model.
     * The trait need to have a method called 'updateAfterTraitname()'
     *
     * @param Model $object
     * @param array $fields
     */
    public function updateAfter($object, $fields)
    {
        foreach (class_uses_recursive(get_called_class()) as $trait) {
            if (method_exists(get_called_class(), $method = 'updateAfter' . class_basename($trait))) {
                $this->$method($object, $fields);
            }
        }
    }

    /**
     * Delete the entity specified by the $id
     *
     * @param  int|string $id [description]
     * @return void
     */
    public function delete($id)
    {
        if (Cache::getDefaultDriver() == 'redis') {
            // Remove collections cache
            Cache::tags('collections')->flush();
        }
		
		if($m = $this->model->find($id)) {
			$m->delete();
		}
    }

    public function massDelete(array $filters = [])
    {
        if (Cache::getDefaultDriver() == 'redis') {
            // Remove collections cache
            Cache::tags('collections')->flush();
        }

        $query = DB::table($this->getModel()->getTable());
        foreach ($filters as $filter) {
            $nFilterTokens = count($filter);
            if (0 == $nFilterTokens) {
                continue;
            }
            if (1 == $nFilterTokens) {
                $query->where($filter[0], null);
            } elseif (2 == $nFilterTokens) {
                $query->where($filter[0], $filter[1]);
            } else {
                $query->where($filter[0], $filter[1], $filter[2]);
            }
        }
		
		$time = $this->model->freshTimestamp();
		
        return $query->update([
			$this->model->getDeletedAtColumn() => $this->model->fromDateTime($time)
		]);
    }

    /**
     * Transform the $fields dates from a user friendly string to a Carbon compatible string.
     * return the altered $fields array
     *
     * @param array $fields
     * @return array
     */
    protected function prepareDatesFields($fields)
    {
        foreach ($this->model->getDates() as $f) {
            if (isset($fields[$f])) {
                if (!empty($fields[$f])) {
                    $fields = $this->prepareDatesField($fields, $f);
                } else {
                    $fields[$f] = null;
                }
            }
        }

        return $fields;
    }

    /**
     * Transform the $fields[$f] date from a user friendly string to a Carbon compatible string.
     * return the altered $fields array
     *
     * @param array $fields
     * @param string $f
     * @return array
     */
    protected function prepareDatesField($fields, $f)
    {
        if ($datetime = \DateTime::createFromFormat("Y-m-d H:i:s", $fields[$f])) {
            $fields[$f] = $datetime->format("Y-m-d H:i:s");
        } elseif (is_numeric($ts = strtotime($fields[$f]))) {
            $fields[$f] = date('Y-m-d H:i:s', $ts);
        } else {
            $fields[$f] = null;
        }

        return $fields;
    }

    /**
     * Prepare $fields for creating/updating the entity Model
     * Transform array key value in a model compatible format.
     * Typically, handle issues such as missing key from unchecked checkboxes or
     * Remark: date formatting are handled separately.
     *
     * @param array $fields
     * @return array
     */
    protected function prepareFieldsBeforeSave($fields)
    {
        foreach ($fields as $key => $value) {
            if (in_array($key, ['published']) ||
                preg_match('/^is_/i', $key) ||
                preg_match('/^has_/i', $key) ||
                preg_match('/^accept_/i', $key)
            ) {
                $fields[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }
        }

        return $fields;
    }

    /**
     * Set the entities position fields according to the order supplied in the $ids array.
     *
     * @param array $ids
     */
    public function setPositions(array $ids)
    {
        if (!empty($ids)) {
            $position = 1;
            foreach ($ids as $id) {
                if (($item = $this->model->find($id)) != null) {
                    $item->position = $position++;
                    $item->save();
                }
            }
        }
    }

    ############## TRANSLATIONS

    /**
     * @param Model $object
     * @param array $fields
     */
    public function createTranslations($object, $fields)
    {
        if (property_exists($this->model, 'translatedAttributes') && !empty($this->model->getTranslatedAttributes())) {
            $languages = array_keys(Config::get('app.locales', []));
            foreach ($languages as $locale) {
                //find active langue
                $translate = $object->translateOrNew($locale);
                if (!isset($fields['active_'.$locale])) {
                    $translate->active = true;
                }

                $this->setTranslateFields($object, $translate, $fields, $locale);
            }
        }
    }

    /**
     * Save $fields whose name match one listed in the Model translatedAttributes
     * post-fixed with "_$locale" to the translations.
     *
     * @param Model $object
     * @param array $fields
     * @return void
     */
    public function updateTranslations($object, $fields)
    {
        if (property_exists($this->model, 'translatedAttributes') && !empty($this->model->getTranslatedAttributes())) {
            $languages = array_keys(Config::get('app.locales', []));
            foreach ($languages as $locale) {
                $this->setTranslateFields($object, $object->translateOrNew($locale), $fields, $locale);
            }
        }
    }

    /**
     * Helper function to handle scopes of translated attributes.
     *
     * @param Builder|Model $query
     * @param array $scopes
     * @param Model $model
     * @return void
     */
    protected function setTranslateScope($query, &$scopes, $model = null)
    {
        if (!$model) {
            $model = $this->model;
        }

        if (property_exists($model, 'translatedAttributes') && !empty($model->getTranslatedAttributes())) {
            $attributes = $model->getTranslatedAttributes();
            $query->whereHas('translations', function ($q) use ($scopes, $attributes, $model) {
                $q->where('locale', isset($scopes['locale']) ? $scopes['locale'] : App::getLocale());
                foreach ($attributes as $attribute) {
                    if ($model != $this->model) {
                        if (array_key_exists($attribute, $scopes)) {
                            $this->filterData($q, $attribute, $scopes[$attribute]);
                        }
                    } else {
                        if (array_key_exists($attribute, $scopes)) {
                            $this->filterData($q, $attribute, $scopes[$attribute]);
                        }
                    }
                }
            });

            //clear relations fields
            foreach ($attributes as $attribute) {
                if (array_key_exists($attribute, $scopes)) {
                    unset($scopes[$attribute]);
                }
            }
        }
    }

    /**
     * Helper function to handle queries of translated attributes.
     *
     * @param Builder|Model $query
     * @param array $queries
     * @param Model $model
     * @return void
     */
    protected function setTranslateQuery($query, &$queries, $model = null)
    {
        if (!$model) {
            $model = $this->model;
        }

        if (property_exists($model, 'translatedAttributes') && !empty($model->getTranslatedAttributes())) {
            $attributes = $model->getTranslatedAttributes();
            $query->whereHas('translations', function ($q) use ($queries, $attributes, $model) {
                $q->where('locale', isset($scopes['locale']) ? $queries['locale'] : App::getLocale());
                foreach ($attributes as $attribute) {
                    if ($model != $this->model) {
                        if (array_key_exists($attribute, $queries)) {
                            $this->queryData($q, $attribute, $queries[$attribute]);
                        }
                    } else {
                        if (array_key_exists($attribute, $queries)) {
                            $this->queryData($q, $attribute, $queries[$attribute]);
                        }
                    }
                }
            });

            //clear relations fields
            foreach ($attributes as $attribute) {
                if (array_key_exists($attribute, $queries)) {
                    unset($queries[$attribute]);
                }
            }
        }
    }

    /**
     * return the altered $fields array with translated attributes.
     *
     * @param Model $object
     * @param array $fields
     * @return array
     */
    private function getFormFieldsTranslations(Model $object, array $fields)
    {
        if ($object->translations != null && $object->getTranslatedAttributes() != null) {
            foreach ($object->translations as $language) {
                foreach ($object->getTranslatedAttributes() as $value) {
                    $fields[$value.'_'.$language->locale] = $language->{$value};
                }
            }
        }

        return $fields;
    }

    /**
     * Save the translated $fields to the specified $object translations for the locale $locale
     *
     * @param Model $object
     * @param Model $translate The $object->translations matching the $locale
     * @param array $fields
     * @param string $locale
     * @return void
     */
    private function setTranslateFields($object, $translate, $fields, $locale)
    {
        foreach ($object->getTranslatedAttributes() as $field) {
            if (array_key_exists("{$field}_{$locale}", $fields)) {
                $translate->{$field} = $fields["{$field}_{$locale}"];
            } elseif (in_array($field, $this->forceSetTranslateFieldBeforeSave)) {
                $translate->{$field} = null;
            }
        }
    }

    /**
     * Add the $object Model active slugs translations to the $fields array
     *
     * @param Model $object
     * @param array $fields
     * @return array the altered $fields array
     */
    private function getFormFieldsSlugs(Model $object, array $fields)
    {
        if ($object->slugs != null) {
            foreach ($object->slugs()->where('active', 1)->get() as $slug) {
                $fields['slug_'.$slug->locale] = $slug->slug;
            }
        }

        return $fields;
    }

    /**
     * Reorganize positions in a collection of items for a $parent_field_name given
     * @param array $fields
     * @param string $parent_field_name
     * @param integer $id
     */
    protected function organizePositions($fields, $parent_field_name = null, $id = null)
    {
        if (isset($fields['position'])) {
            $newPosition = $fields['position'];
            $oldPosition = ($id) ? $this->getById($id)->position : null;

            $objectParentId = null;
            if ($parent_field_name) {
                $objectParentId = ($id) ?
                    $this->getById($id)->$parent_field_name : $this->getById($fields['id'])->$parent_field_name;
            }

            // New entry, add +1 after new position Entities
            // Old entry, position up, add +1 between old and new positions Entities
            // Old entry, position down, add -1 between old and new positions Entities
            if ($newPosition != $oldPosition) {
                if ($parent_field_name) {
                    $filteredItemsCollection = $this->getManyBy($parent_field_name, $objectParentId);
                } else {
                    $filteredItemsCollection = $this->getCollection();
                    $filteredItemsCollection = $filteredItemsCollection->reject(function ($item) use ($id) {
                        if ($item->{$item->getKeyName()} == $id) {
                            return true;
                        }
                        return false;
                    });
                }

                if (!$oldPosition) {
                    $filteredItemsCollection = $filteredItemsCollection->filter(function ($item) use ($newPosition) {
                        return $item['position'] >= $newPosition;
                    });

                    foreach ($filteredItemsCollection as $item) {
                        $item->position = ++$item->position;
                        $item->save();
                    }
                } elseif ($newPosition < $oldPosition) {
                    $filteredItemsCollection = $filteredItemsCollection
                        ->filter(function ($item) use ($newPosition) {
                            return $item['position'] >= $newPosition;
                        })
                        ->filter(function ($item) use ($oldPosition) {
                            return $item['position'] < $oldPosition;
                        })
                    ;

                    foreach ($filteredItemsCollection as $item) {
                        $item->position = ++$item->position;
                        $item->save();
                    }
                } elseif ($newPosition > $oldPosition) {
                    $filteredItemsCollection = $filteredItemsCollection
                        ->filter(function ($item) use ($oldPosition) {
                            return $item['position'] > $oldPosition;
                        })
                        ->filter(function ($item) use ($newPosition) {
                            return $item['position'] <= $newPosition;
                        })
                    ;

                    foreach ($filteredItemsCollection as $item) {
                        $item->position = --$item->position;
                        $item->save();
                    }
                }
            }
        }
    }

    /**
     * sync the $object model many to many relation specified by $relatedFieldName
     * according to the comma separated list of id specified in $fields[$relatedFieldName]
     * the position pivot field is also updated according to the list ordering.
     * additional pivot field can be set to the specified value
     *
     * @param Model $object
     * @param array $fields
     * @param string $relatedFieldName
     * @param array $pivotValues additional pivot field to set ['pivotfieldname' => value, ...]
     * @param array $linkedFieldNames
     * @return void
     */
    public function updateRelatedElements(
        Model $object,
        array $fields,
        $relatedFieldName,
        array $pivotValues = [],
        $linkedFieldNames = []
    ) {
        if (array_key_exists($relatedFieldName, $fields)) {
            $relatedElements = [];
            if (isset($fields[$relatedFieldName]) && !empty($fields[$relatedFieldName])) {
                $relatedElements = (is_array($fields[$relatedFieldName])) ?
                    $fields[$relatedFieldName] : explode(',', $fields[$relatedFieldName]);
            }

            // Reformat $relatedFieldName in expected $relatedObjectName
            $relatedObjectName = str_replace('_id', '', $relatedFieldName);
            // Camelize field
            $relatedObjectName = str_replace('_', '', ucwords($relatedObjectName, '_'));

            $relatedObjectRelation = $object->$relatedObjectName();

            try {
                // Check if relatedElement needs to be updated with position or not
                if (Schema::hasColumn($relatedObjectRelation->getTable(), 'position')) {
                    $relatedElementsWithPosition = [];
                    $position = 1;
                    foreach ($relatedElements as $relatedElement) {
                        $relatedElementsWithPosition[$relatedElement] = ['position' => $position++] + $pivotValues;
                    }

                    $relatedObjectRelation->sync($relatedElementsWithPosition);
                } else {
                    $relatedElementsWithoutPosition = [];
                    foreach ($relatedElements as $relatedElement) {
                        $relatedElementsWithoutPosition[$relatedElement] = $pivotValues;
                    }

                    // If pivotValues indicated, detach only items with this pivot values else, sync all
                    if ($pivotValues) {
                        $detach_ids = $relatedObjectRelation->pluck('id')
                            ->diff(array_keys($relatedElementsWithoutPosition))->all();
                        $attach_ids = collect(array_keys($relatedElementsWithoutPosition))
                            ->diff($relatedObjectRelation->pluck('id'))->all();

                        $relatedObjectRelation->detach($detach_ids);

                        if ($linkedFieldNames) {
                            // If linkedFieldNames, remove duplicate content from linked fields
                            foreach ($linkedFieldNames as $linkedFieldName) {
                                // Reformat $relatedFieldName in expected $relatedObjectName
                                $linkedObjectName = str_replace('_id', '', $linkedFieldName);
                                // Camelize field
                                $linkedObjectName = str_replace('_', '', ucwords($linkedObjectName, '_'));

                                $linkedObjectRelation = $object->$linkedObjectName();

                                $detach_linked_ids = $linkedObjectRelation->pluck('id')
                                    ->intersect(array_keys($relatedElementsWithoutPosition))->all();

                                $linkedObjectRelation->detach($detach_linked_ids);
                            }
                        }

                        $relatedObjectRelation->attach($attach_ids, $pivotValues);
                    } else {
                        $relatedObjectRelation->sync($relatedElementsWithoutPosition);
                    }
                }
            } catch (\Exception $e) {
                throw new RelationNotFoundException(
                    'No query result for model [' . get_class($object->$relatedObjectName()->getRelated()) .
                    '] ' . implode(' or ', array_values($relatedElements))
                );
            }
        }
    }

    /**
     * Retrieve last published items with limit
     * Check if published is true
     * And if publication_started_at and publication_ended_at are setted and valid for current date
     * @param $limit
     * @param array $idsToExclude
     * @param array $filters
     * @param bool $accumulativeFilters
     * @return \Illuminate\Support\Collection
     */
    public function findLastPublished($limit = null, $idsToExclude = [], $filters = [], $accumulativeFilters = true)
    {
        $tableName = $this->getModel()->getTable();
        $path = explode('\\', get_class($this->getModel()));
        $modelName = array_pop($path);
        $singularTableName = mb_strtolower($modelName);

        $path = explode('\\', get_class($this->getModel()));
        $modelName = array_pop($path);

        $query = DB::table($tableName)
            ->distinct()
            ->select(
                DB::raw('"' . mb_strtolower($modelName) . '" as type'),
                $tableName . '.id as id',
                $tableName . '.created_at as created_at'
            )
        ;

        $fillables = $this->getModel()->getFillable();

        if (in_array('published', $fillables)) {
            $query->where($tableName . '.published', true);
        }

        if (in_array('is_standalone', $fillables)) {
            $query->where($tableName . '.is_standalone', true);
        }

        if (in_array('publication_started_at', $fillables) &&
            in_array('publication_ended_at', $fillables)
        ) {
            $query
                ->addSelect($tableName . '.publication_started_at as publication_started_at')
                ->where(function ($query) use ($tableName) {
                    $query
                        ->where($tableName . '.publication_started_at', null)
                        ->orWhere($tableName . '.publication_started_at', '<=', Carbon::now());
                })
                ->where(function ($query) use ($tableName) {
                    $query
                        ->where($tableName . '.publication_ended_at', null)
                        ->orWhere($tableName . '.publication_ended_at', '>=', Carbon::now());
                })
                ->orderBy($tableName . '.publication_started_at', 'desc')
            ;
        }

        if ($idsToExclude) {
            $query->whereNotIn($tableName . '.id', $idsToExclude);
        }

        if ($filters) {
            foreach ($filters as $column => $dataToFilter) {
                if ($column == 'tag' && Schema::hasTable($singularTableName . '_tag')) {
                    if (!$accumulativeFilters) {
                        $query->leftJoin(
                            $singularTableName . '_tag',
                            $singularTableName . '_tag.' . $singularTableName . '_id',
                            '=',
                            $tableName . '.id'
                        );
                        $query->whereIn($singularTableName . '_tag.tag_id', $dataToFilter['ids']);
                        if (isset($dataToFilter['primary'])) {
                            $query->where($singularTableName . '_tag.is_primary', $dataToFilter['primary']);
                        }
                    } else {
                        $nbLoop = 0;
                        foreach ($dataToFilter['ids'] as $value) {
                            $nbLoop = ++$nbLoop;
                            $query
                                ->leftJoin(
                                    $singularTableName . '_tag as associatedTag' . $nbLoop,
                                    'associatedTag' . $nbLoop . '.' . $singularTableName . '_id',
                                    '=',
                                    $tableName . '.id'
                                )
                                ->where('associatedTag' . $nbLoop . '.tag_id', $value)
                            ;
                            if (isset($dataToFilter['primary'])) {
                                $query->where('associatedTag' . $nbLoop . 'is_primary', $dataToFilter['primary']);
                            }
                        }
                    }
                } elseif ($column == 'theme') {
                    if (Schema::hasTable($singularTableName.'_theme')) {
                        if (!$accumulativeFilters) {
                            $query->leftJoin(
                                $singularTableName.'_theme',
                                $singularTableName.'_theme.'.$singularTableName.'_id',
                                '=',
                                $tableName.'.id'
                            );
                            $query->whereIn($singularTableName.'_theme.theme_id', $dataToFilter['ids']);
                        } else {
                            $nbLoop = 0;
                            foreach ($dataToFilter['ids'] as $value) {
                                $nbLoop = ++$nbLoop;
                                $query
                                    ->leftJoin(
                                        $singularTableName . '_theme as associatedTheme' . $nbLoop,
                                        'associatedTheme' . $nbLoop . '.' . $singularTableName . '_id',
                                        '=',
                                        $tableName . '.id'
                                    )
                                    ->where('associatedTheme' . $nbLoop . '.theme_id', $value)
                                ;
                            }
                        }

                    }
                } elseif ($column == 'duration') {
                    if (in_array('duration', $fillables)) {
                        $query->where('duration', $dataToFilter['operator'], $dataToFilter['value']);
                    }
                } else {
                    $query->whereIn($tableName . '.' . $column, [$dataToFilter]);
                }
            }
        }

        if (property_exists($this->getModel(), 'translatedAttributes') && !empty($this->getModel()->getTranslatedAttributes())) {
            if (in_array('title', $this->getModel()->getTranslatedAttributes())) {
                $query->addSelect('title');
            }

            $query->leftJoin(
                $singularTableName . '_translations',
                $singularTableName . '_translations.' . $singularTableName . '_id',
                '=',
                $tableName . '.id'
            );
            $query->where(
                $singularTableName . '_translations.locale',
                '=',
                request('lang') ? request('lang') : App::getLocale()
            );
            $query->where($singularTableName . '_translations.active', '=', true);
        }

        $query->orderBy($tableName . '.created_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }
}

<?php

namespace App\Repositories;

use App\Contracts\Repositories\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App;
use Config;
use Schema;

abstract class AbstractRepository implements RepositoryInterface
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
     * Fetch a list a list of entities paginated
     *
     * @param  integer $take number of item per page
     * @param  array $scopes list of scope the entity need to match
     * [field => value,...]
     * @param  array|string $with list of relations to eager load
     * ['translations', 'artists', 'artists.translations',...]
     * @param  array $orders
     * ['fieldname1' => 'asc', 'fieldname2' => 'desc',...]
     * @return LengthAwarePaginator
     */
    public function getPaginateCollection($take = 5, $scopes = [], $with = [], $orders = [])
    {
        return $this->setScopesAndOrders(
            $this->make($with),
            $scopes,
            $orders
        )->paginate($take);
    }

    /**
     * Fetch a list a list of entities paginated
     *
     * @param int $take  number of item per page
     * @param array $scopes list of scope the entity need to match
     * [field => value,...]
     * @param array $with  list of relations to eager load
     * @param array $orders  list of field to sort on ['field1' => 'asc', 'field2' => 'desc',...]
     * @param integer|null $except  Do not return the entity with that id
     * @return LengthAwarePaginator
     */
    public function getPaginateCollectionForResourceBrowser(
        $take = 5,
        $scopes = [],
        $with = [],
        $orders = [],
        $except = null
    ) {
        $query = $this->setScopesAndOrders(
            $this->make($with+['mediasWithAttributes', 'translations']),
            $scopes,
            $orders
        );

        if ($except != null) {
            $query->where('id', '!=', $except);
        }

        return $query->paginate($take);
    }

    /**
     * Fetch a Collection of entities
     *
     * @param int $take  number of item to take
     * @param array $scopes  list of scope the entity need to match [field => value,...]
     * @param array $with  list of relations to eager load
     * @param array  $orders  list of field to sort on ['field1' => 'asc', 'field2' => 'desc',...]
     * @return Collection
     */
    public function getCollection($take = 5, $scopes = [], $with = [], $orders = [])
    {
        return $this->setScopesAndOrders($this->make($with), $scopes, $orders)->take($take)->get();
    }

    /**
     * Fetch an array of $field values indexed by the $key values.
     * Note that the 2 fields are filtered afterward.
     * If the entities do not need to be loaded, use getListFast instead.
     *
     * @param string $field  Field name we want to return in the list
     * @param string $key  Key field name we want to return in the list
     * @param array $scopes  list of scope the entity need to match [field => value,...]
     * @param array $with  list of relations to eager load
     * @param array $orders  list of field to sort on ['field1' => 'asc', 'field2' => 'desc',...]
     * @return Collection
     */
    public function getList($field, $key = 'id', $scopes = [], $with = [], $orders = [])
    {
        return $this->setScopesAndOrders(
            $this->make($with),
            $scopes,
            $orders
        )->get()->lists($field, $key)->all();
    }

    /**
     * Apply scopes filters and orders sorts to a query.
     * It return the modified query for chaining.
     *
     * @param Builder $query
     * @param array $scopes list of scope the entity need to match [field => value,...]
     * @param array $orders list of field to sort on ['field1' => 'asc', 'field2' => 'desc',...]
     * @return Builder  list of field to sort on ['field1' => 'asc', 'field2' => 'desc',...]
     */
    public function setScopesAndOrders($query, array $scopes = [], array $orders = [])
    {
        $this->setTranslateScope($query, $scopes);
        foreach ($scopes as $key => $value) {
            if (is_array($value)) {
                $query->whereIn($key, $value);
            } else {
                if (strpos($value, '%%') !== false) {
                    $query->where($key, 'LIKE', str_replace('%%', '%', $value));
                } else {
                    $query->where($key, $value);
                }
            }
        }

        $this->setTranslateOrder($query, $scopes, $orders);
        foreach ($orders as $key => $value) {
            $query->orderBy($key, $value);
        }

        return $query;
    }

    /**
     * Alter the query to apply the specified sort order on translated attributes.
     *
     * @param Builder $query
     * @param array $scopes  list of scope the entity need to match [field => value,...]
     * @param array $orders
     */
    public function setTranslateOrder($query, $scopes, &$orders)
    {
        if (property_exists($this->model, 'translatedAttributes')) {
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
     * Find an entity by id
     *
     * @param int $id
     * @param array|string $with
     * @return Model|\stdClass|array Which is normally a Model.
     */
    public function getById($id, array $with = [])
    {
        return $this->make($with)->findOrFail($id);
    }

    /**
     * Find an entity by id with scope
     *
     * @param int $id
     * @param array $scopes
     * @param array|string $with
     * @return Model|\stdClass|array|null
     */
    public function getByIdWithScope($id, $scopes = [], array $with = [])
    {
        return $this->setScopesAndOrders($this->make($with), $scopes)->find($id);
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
        return $this->make($with)->where($key, '=', $value)->first();
    }

    /**
     * Find a single entity by scope
     * @param array $scopes
     * @param array $with
     * @return array|null|\stdClass
     */
    public function getFirstByScope($scopes = [], array $with = [])
    {
        return $this->setScopesAndOrders($this->make($with), $scopes)->first();
    }

    /**
     * Find a single entity by key value
     *
     * @param array $with
     * @return \stdClass|array|null
     */
    public function getFirst(array $with = [])
    {
        return $this->make($with)->first();
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
        return $this->make($with)->where($key, '=', $value)->get();
    }

    /**
     * [create description]
     * @param  array $fields [description]
     * @return Model         [description]
     */
    public function create($fields)
    {
        $object = $this->model->create($this->prepareDatesFields($this->prepareFieldsBeforeSave($fields)));

        $this->createTranslations($object, $fields);

        $this->createSlugs($object, $fields);

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
        $fields = $this->prepareFieldsBeforeSave($this->prepareDatesFields($fields));

        $object = $this->model->findOrFail($id);

        $this->updateTranslations($object, $fields);

        $this->updateSlugs($object, $fields);

        // Set empty field to NULL
        foreach ($fields as $key => $value) {
            if (empty($value)) {
                $fields[$key] = null;
            }
        }

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
        if (($object = $this->model->find($id)) != null) {
            $object->delete();
        }
    }

    /**
     * [getFormFields description]
     * @return array [description]
     */
    public function getFormFields($object)
    {
        if (method_exists($object, 'toArrayNative')) {
            $fields = $this->getDatesFields($object->toArrayNative(), $object);
        } else {
            $fields = $this->getDatesFields($object->toArray(), $object);
        }

        $fields = $this->getFormFieldsTranslations($object, $fields);

        return $this->getFormFieldsSlugs($object, $fields);
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
        if (($datetime=DateTime::createFromFormat("d/m/Y", $fields[$f]))) {
            $fields[$f] = $datetime->format("Y-m-d");
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
        if (!isset($fields['published'])) {
            $fields['published'] = 0;
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
        $this->updateTranslations($object, $fields);
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
        if (property_exists($this->model, 'translatedAttributes')) {
            $languages = array_keys(Config::get('app.locales', []));
            foreach ($languages as $locale) {
                //find active langue
                $translate = $object->translateOrNew($locale);
                if (!isset($fields['active_'.$locale])) {
                    $translate->active = 0;
                } else {
                    $translate->active = 1;
                }

                $this->setTranslateFields($object, $translate, $fields, $locale);
            }
        }
    }

    /**
     * Helper function to handle scopes of translated attributes.
     *
     * @param Builder|Model $query
     * @param array $scopes
     * @return void
     */
    protected function setTranslateScope($query, &$scopes)
    {
        if (property_exists($this->model, 'translatedAttributes')) {
            $attributes = $this->model->getTranslatedAttributes();
            $query->whereHas('translations', function ($q) use ($scopes, $attributes) {
                $q->where('locale', isset($scopes['locale']) ? $scopes['locale'] : App::getLocale());
                foreach ($attributes as $attribute) {
                    if (array_key_exists($attribute, $scopes) &&
                        (is_string($scopes[$attribute]) || $scopes[$attribute] == null)) {
                        if (strpos($scopes[$attribute], '%%') !== false) {
                            $q->where($attribute, 'LIKE', str_replace('%%', '%', $scopes[$attribute]));
                        } elseif ($scopes[$attribute] == null) {
                            $q->where($attribute, $scopes[$attribute]);
                        } else {
                            $q->where($attribute, 'LIKE', '%'.$scopes[$attribute].'%');
                        }
                    }
                }
            });

            #clear relations fields
            foreach ($attributes as $attribute) {
                if (array_key_exists($attribute, $scopes)) {
                    unset($scopes[$attribute]);
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
        if ($object->translations != null && $object->translatedAttributes != null) {
            foreach ($object->translations as $language) {
                foreach ($object->translatedAttributes as $value) {
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
        foreach ($object->translatedAttributes as $field) {
            if (isset($fields["{$field}_{$locale}"])) {
                $translate->{$field} = $fields["{$field}_{$locale}"];
            } elseif (in_array($field, $this->forceSetTranslateFieldBeforeSave)) {
                $translate->{$field} = null;
            }
        }
    }

    ########## SLUGS

    /**
     * @param Model $object
     * @param array $fields
     * @return void
     */
    public function createSlugs($object, $fields)
    {
        if (property_exists($this->model, 'slugAttributes')) {
            foreach (array_keys(Config::get('app.locales', [])) as $locale) {
                $this->createOneSlug($object, $fields, $locale);
            }
        }
    }

    /**
     * @param Model $object
     * @param array $fields
     * @param string $locale
     * @return void
     */
    private function createOneSlug($object, $fields, $locale)
    {
        $newSlug = [];

        if (isset($fields['slug_'.$locale]) && !empty($fields['slug_'.$locale])) {
            $newSlug['slug'] = $fields['slug_'.$locale];
        } elseif (isset($fields[reset($object->slugAttributes).'_'.$locale]) && isset($fields['active_'.$locale])) {
            $newSlug['slug'] = $fields[reset($object->slugAttributes).'_'.$locale];
        }

        if (!empty($newSlug)) {
            $newSlug['locale'] = $locale;
            $newSlug = $this->getSlugParameters($object, $fields, $newSlug);
            $object->updateOrNewSlug($newSlug);
        }
    }

    /**
     * @param Model $object
     * @param array $fields
     */
    public function updateSlugs(Model $object, array $fields)
    {
        if (property_exists($this->model, 'slugAttributes')) {
            foreach (array_keys(Config::get('app.locales', [])) as $locale) {
                // Find active language
                if (($slug=$object->getSlugObject($locale))==null) {
                    $this->createOneSlug($object, $fields, $locale);
                } elseif (isset($fields['slug_'.$locale]) && !empty($fields['slug_'.$locale])) {
                    if (!isset($fields['active_'.$locale])) {
                        $object->unactiveSlugsOfLocale($locale);
                    } else {
                        $currentSlug = [];
                        $currentSlug['slug'] = $fields['slug_'.$locale];
                        $currentSlug['locale'] = $locale;
                        $currentSlug = $this->getSlugParameters($object, $fields, $currentSlug);
                        $object->updateOrNewSlug($currentSlug);  #update slug verify existing slug
                    }
                }
            }
        }
    }

    /**
    * @param Model $object
    * @param array $fields
    * @param array $slug
    * @return array
    */
    protected function getSlugParameters(Model $object, array $fields, $slug)
    {
        $slugParams = $object->getSlugParams($slug['locale']);
        foreach ($object->slugAttributes as $param) {
            if (isset($slugParams[$param]) && isset($fields[$param])) {
                $slug[$param] = $fields[$param];
            } elseif (isset($slugParams[$param]) && isset($slugParams[$param])) {
                $slug[$param] = $slugParams[$param];
            }
        }

        return $slug;
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
    protected function organizePositions($fields, $parent_field_name, $id = null)
    {
        $newPosition = $fields['position'];
        $oldPosition = ($id) ? $this->getById($id)->position : null;

        $objectParentId = ($id) ? $this->getById($id)->$parent_field_name : $this->getById($fields['id'])->question_id;

        // New entry, add +1 after new position Entities
        // Old entry, position up, add +1 between old and new positions Entities
        // Old entry, position down, add -1 between old and new positions Entities
        if ($newPosition != $oldPosition) {
            $filteredItemsCollection = $this->getManyBy($parent_field_name, $objectParentId);

            if (!$oldPosition) {
                $filteredItemsCollection->filter(function ($item) use ($newPosition) {
                    return $item['position'] >= $newPosition;
                });

                foreach ($filteredItemsCollection as $item) {
                    $item->position = ++$item->position;
                    $item->save();
                }
            } elseif ($newPosition < $oldPosition) {
                $filteredItemsCollection
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
                $filteredItemsCollection
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
     * @return void
     * @throws \Exception
     */
    public function updateRelatedElements(Model $object, array $fields, $relatedFieldName, array $pivotValues = [])
    {
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

            // Check if relatedElement needs to be updated with position or not
            if (Schema::hasColumn($relatedObjectRelation->getTable(), 'position')) {
                $relatedElementsWithPosition = [];
                $position = 1;
                foreach ($relatedElements as $relatedElement) {
                    $relatedElementsWithPosition[$relatedElement] = ['position' => $position++] + $pivotValues;
                }

                $relatedObjectRelation->sync($relatedElementsWithPosition);
            } else {
                $relatedObjectRelation->sync($relatedElements);
            }
        }
    }
}

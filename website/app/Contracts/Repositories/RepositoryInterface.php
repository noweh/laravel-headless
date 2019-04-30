<?php namespace app\Contracts\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;


interface RepositoryInterface
{

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
    public function getPaginateCollection($take = 5, $scopes = [], $with = [], $orders = []);

    /**
     * Fetch a list a list of entities paginated
     *
     * @param int $take  number of item per page
     * @param array $scopes list of scope the entity need to match
     * [field => value,...]
     * @param array|string $with  list of relations to eager load
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
    );

    /**
     * Fetch a Collection of entities
     *
     * @param int $take  number of item to take
     * @param array $scopes  list of scope the entity need to match [field => value,...]
     * @param array|string $with  list of relations to eager load
     * @param array  $orders  list of field to sort on ['field1' => 'asc', 'field2' => 'desc',...]
     * @return Collection
     */
    public function getCollection($take = 5, $scopes = [], $with = [], $orders = []);

    /**
     * Fetch a 2 fields Collection one of them being a key.
     * Note that the 2 fields are filtered afterward.
     * If the entities do not need to be loaded, use getListFast instead.
     *
     * @param string $field  Field name we want to return in the list
     * @param string $key  Key field name we want to return in the list
     * @param array $scopes  list of scope the entity need to match [field => value,...]
     * @param array|string $with  list of relations to eager load
     * @param array $orders  list of field to sort on ['field1' => 'asc', 'field2' => 'desc',...]
     * @return Collection
     */
    public function getList($field, $key = 'id', $scopes = [], $with = [], $orders = []);

    /**
     * Apply scopes filters and orders sorts to a query.
     * It return the modified query for chaining.
     *
     * @param Builder $query
     * @param array $scopes list of scope the entity need to match [field => value,...]
     * @param array $orders list of field to sort on ['field1' => 'asc', 'field2' => 'desc',...]
     * @return Builder  list of field to sort on ['field1' => 'asc', 'field2' => 'desc',...]
     */
    public function setScopesAndOrders($query, array $scopes = [], array $orders = []);

    /**
     * Find an entity by id
     *
     * @param int $id
     * @param array|string $with
     * @return Model|\stdClass|array Which is normally a Model.
     */
    public function getById($id, array $with = []);

    /**
     * Find an entity by id with scope
     *
     * @param int $id
     * @param array $scopes
     * @param array|string $with
     * @return Model|\stdClass|array|null
     */
    public function getByIdWithScope($id, $scopes = [], array $with = []);

    /**
     * Find a single entity by key value
     *
     * @param string $key
     * @param string $value
     * @param array|string $with
     * @return \stdClass|array|null
     */
    public function getFirstBy($key, $value, array $with = []);

    /**
     * Find a single entity by scope
     * @param array $scopes
     * @param array|string $with
     * @return \stdClass|array|null
     */
    public function getFirstByScope($scopes = [], array $with = []);

    /**
     * Find a single entity by key value
     *
     * @param string $key
     * @param string $value
     * @param array|string $with
     * @return \stdClass|array|null
     */
    public function getFirst(array $with = []);

    /**
     * Find many entities by key value
     *
     * @param string $key fieldname
     * @param int|string $value
     * @param array|string $with
     * @return Collection
     */
    public function getManyBy($key, $value, array $with = []);

    /**
     * Create the entity, but also prepare de $fields, and process all related info like
     * translations, slug, medias, SEO, ...
     *
     * @param  array $fields
     * @return Model
     */
    public function create($fields);

    /**
     * Update the entity specified by $id, but also prepare de $fields, and process all related info like
     * translations, slug, medias, SEO, ...
     *
     * @param  int|string $id
     * @param  array $fields
     * @return Model
     */
    public function update($id, $fields);

    /**
     * Postprocessing of the related info included as traits in the Model.
     * The trait need to have a method called 'updateAfterTraitname()'
     *
     * @param Model $object
     * @param array $fields
     */
    public function updateAfter($object, $fields);

    /**
     * Delete the entity specified by the $id
     *
     * @param  int|string $id Entity key value
     * @return void
     */
    public function delete($id);

    /**
     * Return the Model as an array
     *
     * @param Model $object
     * @return array
     */
    public function getFormFields($object);


    /**
     * Prepare $fields for creating/updating the entity Model
     * Transform array key value in a model compatible format.
     * Typically, handle issues such as missing key from unchecked checkboxes or
     * Remark: date formatting are handled separately.
     *
     * @param array $fields
     * @return array
     */
    //protected function prepareFieldsBeforeSave(array $fields)

    /**
     * Set the entities position fields according to the order supplied in the $ids array.
     *
     * @param array $ids
     */
    public function setPositions(array $ids);

    /**
     * Save $fields whose name match one listed in the Model translatedAttributes
     * post-fixed with "_$locale" to the translations.
     *
     * @param Model $object
     * @param array $fields
     */
    public function createTranslations($object, $fields);

    /**
     * Save $fields whose name match one listed in the Model translatedAttributes
     * post-fixed with "_$locale" to the translations.
     *
     * @param Model $object
     * @param array $fields
     * @return void
     */
    public function updateTranslations($object, $fields);

    /**
     * Helper function to handle scopes of translated attributes.
     *
     * @param Builder $query
     * @param array $scopes
     */
    //protected function setTranslateScope(Builder $query, array &$scopes)

    /**
     * return the altered $fields array with translated attributes.
     *
     * @param Model $object
     * @param array $fields
     * @return array
     */
    //public function getFormFieldsTranslations(Model $object, array $fields);

    /**
     * Save the translated $fields to the specified $object translations for the locale $locale
     *
     * @param Model $object
     * @param Model $translate The $object->translations matching the $locale
     * @param array $fields
     * @param string $locale
     * @return void
     *
    private function setTranslateFields(Model $object, $translate, array $fields, $locale); */

    ########## SLUGS

    /**
     * @param Model $object
     * @param array $fields
     * @return void
     */
    public function createSlugs($object, $fields);

    /**
     * @param Model $object
     * @param array $fields
     * @param string $locale
     * @return void
     *
    private function createOneSlug($object, $fields, $locale); */

    /**
     * @param Model $object
     * @param array $fields
     */
    public function updateSlugs(Model $object, array $fields);

    /**
     * @param Model $object
     * @param array $fields
     * @param string $slug
     * @return array
     *
    protected function getSlugParameters(Model $object, array $fields, $slug); */

    /**
     * Add the $object Model active slugs translations to the $fields array
     *
     * @param Model $object
     * @param array $fields
     * @return array the altered $fields array
     */
    //private function getFormFieldsSlugs(Model $object, array $fields);
    //public function getFormFieldsSlugs($object, $fields);

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
     */
    public function updateRelatedElements(Model $object, array $fields, $relatedFieldName, array $pivotValues = []);

}
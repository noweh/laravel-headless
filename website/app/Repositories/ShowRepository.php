<?php

namespace App\Repositories;

use App\Models\Show;
use Illuminate\Database\Eloquent\RelationNotFoundException;

class ShowRepository extends AbstractRepository
{
    public function __construct(Show $model)
    {
        $this->model = $model;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     * @param array $fields
     * @throws RelationNotFoundException
     */
    public function updateAfter($object, $fields)
    {
        parent::updateAfter($object, $fields);
    }
}

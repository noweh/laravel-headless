<?php

namespace App\Repositories;

use App\Contracts\Repositories\SessionRepositoryInterface;
use App\Models\Session;

class SessionRepository extends AbstractRepository implements SessionRepositoryInterface
{
    public function __construct(Session $model)
    {
        $this->model = $model;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     * @param array $fields
     * @throws \Exception
     */
    public function updateAfter($object, $fields)
    {
        parent::updateAfter($object, $fields);

        $this->updateRelatedElements($object, $fields, 'themes_id');
    }
}

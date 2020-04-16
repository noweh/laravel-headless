<?php

namespace App\Repositories;

use App\Models\MediaLibrary;
use Illuminate\Database\Eloquent\RelationNotFoundException;

class MediaLibraryRepository extends AbstractRepository
{
    public function __construct(MediaLibrary $model)
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

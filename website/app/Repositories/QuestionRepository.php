<?php

namespace App\Repositories;

use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Models\Question;

class QuestionRepository extends AbstractRepository implements QuestionRepositoryInterface
{
    public function __construct(Question $model)
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
    }
}

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
}

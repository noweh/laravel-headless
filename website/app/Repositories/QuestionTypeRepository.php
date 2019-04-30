<?php

namespace App\Repositories;

use App\Contracts\Repositories\QuestionTypeRepositoryInterface;
use App\Models\QuestionType;

class QuestionTypeRepository extends AbstractRepository implements QuestionTypeRepositoryInterface
{
    public function __construct(QuestionType $model)
    {
        $this->model = $model;
    }
}

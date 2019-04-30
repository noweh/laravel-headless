<?php

namespace App\Repositories;

use App\Contracts\Repositories\PossibleAnswerRepositoryInterface;
use App\Models\PossibleAnswer;

class PossibleAnswerRepository extends AbstractRepository implements PossibleAnswerRepositoryInterface
{
    public function __construct(PossibleAnswer $model)
    {
        $this->model = $model;
    }
}

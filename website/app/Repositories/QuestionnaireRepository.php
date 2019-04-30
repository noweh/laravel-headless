<?php

namespace App\Repositories;

use App\Contracts\Repositories\QuestionnaireRepositoryInterface;
use App\Models\Questionnaire;

class QuestionnaireRepository extends AbstractRepository implements QuestionnaireRepositoryInterface
{
    public function __construct(Questionnaire $model)
    {
        $this->model = $model;
    }
}

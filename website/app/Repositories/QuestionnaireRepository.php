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

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     * @param array $fields
     * @throws \Exception
     */
    public function updateAfter($object, $fields)
    {
        parent::updateAfter($object, $fields);

        $this->updateRelatedElements($object, $fields, 'themes_id');
        $this->updateRelatedElements($object, $fields, 'questions_id');
    }
}

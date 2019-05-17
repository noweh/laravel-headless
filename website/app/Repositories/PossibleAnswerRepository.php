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

    /**
     * @param array $fields
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create($fields)
    {
        $this->organizePositions($fields, 'question_id');

        return parent::create($fields);
    }

    /**
     * @param int|string $id
     * @param array $fields
     * @return \Illuminate\Database\Eloquent\Model|void
     */
    public function update($id, $fields)
    {
        $this->organizePositions($fields, 'question_id', $id);

        parent::update($id, $fields);
    }
}

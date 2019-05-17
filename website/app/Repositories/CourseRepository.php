<?php

namespace App\Repositories;

use App\Contracts\Repositories\CourseRepositoryInterface;
use App\Models\Course;

class CourseRepository extends AbstractRepository implements CourseRepositoryInterface
{
    public function __construct(Course $model)
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
        $this->updateRelatedElements($object, $fields, 'questionnaires_id');
        $this->updateRelatedElements($object, $fields, 'courses_id');
    }
}

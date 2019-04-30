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
}

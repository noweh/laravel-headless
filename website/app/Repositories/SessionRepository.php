<?php

namespace App\Repositories;

use App\Contracts\Repositories\SessionRepositoryInterface;
use App\Models\Session;

class SessionRepository extends AbstractRepository implements SessionRepositoryInterface
{
    public function __construct(Session $model)
    {
        $this->model = $model;
    }
}

<?php

namespace App\Repositories;

use App\Models\Mediable;

class MediableRepository extends AbstractRepository
{
    public function __construct(Mediable $model)
    {
        $this->model = $model;
    }
}

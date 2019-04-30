<?php

namespace App\Repositories;

use App\Contracts\Repositories\ModuleRepositoryInterface;
use App\Models\Module;

class ModuleRepository extends AbstractRepository implements ModuleRepositoryInterface
{
    public function __construct(Module $model)
    {
        $this->model = $model;
    }
}

<?php

namespace App\Repositories;

use App\Contracts\Repositories\ThemeRepositoryInterface;
use App\Models\Theme;

class ThemeRepository extends AbstractRepository implements ThemeRepositoryInterface
{
    public function __construct(Theme $model)
    {
        $this->model = $model;
    }
}

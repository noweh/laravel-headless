<?php

namespace App\Repositories;

use App\Models\AdminUser;

class AdminUserRepository extends AbstractRepository
{
    public function __construct(AdminUser $model)
    {
        $this->model = $model;
    }
}

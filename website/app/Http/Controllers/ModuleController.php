<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\ModuleRepositoryInterface;
use Illuminate\Http\Request;

class ModuleController extends AbstractController
{
    public function __construct(
        Request $request,
        ModuleRepositoryInterface $repository
    ) {
        $this->request = $request;
        $this->repository = $repository;

        parent::__construct();
    }
}

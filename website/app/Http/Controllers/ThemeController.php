<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\ThemeRepositoryInterface;
use Illuminate\Http\Request;

class ThemeController extends AbstractController
{
    public function __construct(
        Request $request,
        ThemeRepositoryInterface $repository
    ) {
        $this->request = $request;
        $this->repository = $repository;

        parent::__construct();
    }
}

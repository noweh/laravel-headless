<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\SessionRepositoryInterface;
use Illuminate\Http\Request;

class SessionController extends AbstractController
{
    public function __construct(
        Request $request,
        SessionRepositoryInterface $repository
    ) {
        $this->request = $request;
        $this->repository = $repository;

        parent::__construct();
    }
}

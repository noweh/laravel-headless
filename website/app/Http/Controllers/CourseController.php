<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\CourseRepositoryInterface;
use Illuminate\Http\Request;

class CourseController extends AbstractController
{
    public function __construct(
        Request $request,
        CourseRepositoryInterface $repository
    ) {
        $this->request = $request;
        $this->repository = $repository;

        parent::__construct();
    }
}

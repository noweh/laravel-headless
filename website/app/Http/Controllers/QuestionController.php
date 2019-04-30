<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\QuestionRepositoryInterface;
use Illuminate\Http\Request;

class QuestionController extends AbstractController
{
    public function __construct(
        Request $request,
        QuestionRepositoryInterface $repository
    ) {
        $this->request = $request;
        $this->repository = $repository;

        parent::__construct();
    }
}

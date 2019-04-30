<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\QuestionTypeRepositoryInterface;
use Illuminate\Http\Request;

class QuestionTypeController extends AbstractController
{
    public function __construct(
        Request $request,
        QuestionTypeRepositoryInterface $repository
    ) {
        $this->request = $request;
        $this->repository = $repository;

        parent::__construct();
    }
}

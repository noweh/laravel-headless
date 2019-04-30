<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\PossibleAnswerRepositoryInterface;
use Illuminate\Http\Request;

class PossibleAnswerController extends AbstractController
{
    public function __construct(
        Request $request,
        PossibleAnswerRepositoryInterface $repository
    ) {
        $this->request = $request;
        $this->repository = $repository;

        parent::__construct();
    }
}

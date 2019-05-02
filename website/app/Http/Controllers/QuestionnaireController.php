<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\QuestionnaireRepositoryInterface;
use Illuminate\Http\Request;

class QuestionnaireController extends AbstractController
{
    public function __construct(
        Request $request,
        QuestionnaireRepositoryInterface $repository
    ) {
        $this->request = $request;
        $this->repository = $repository;

        parent::__construct();
    }

    /**
     * @OA\Get(
     *     path="/questionnaires",
     *     tags={"Questionnaire"},
     *     summary="Retrieve a listing of questionnaires.",
     *     @OA\Parameter(
     *         name="lang",
     *         required=true,
     *         in="query",
     *         description="Language",
     *         @OA\Schema(
     *             type="string",
     *             enum={"fr","en"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="published",
     *         required=false,
     *         in="query",
     *         description="Filter on published/not published elements",
     *         @OA\Schema(
     *             type="boolean",
     *             enum={true,false}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         required=false,
     *         in="query",
     *         description="Name of attribute to ORDER BY",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sortOrder",
     *         required=false,
     *         in="query",
     *         description="Clause for the ORDER BY",
     *         @OA\Schema(
     *             type="string",
     *             enum={"asc","desc"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Display a listing of questionnaires."
     *     )
     * )
     */
}

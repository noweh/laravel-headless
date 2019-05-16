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

    /**
     * @OA\Get(
     *     path="/possibleAnswers",
     *     tags={"PossibleAnswer"},
     *     summary="Retrieve a listing of PossibleAnswers.",
     *     description="Returns a listing of PossibleAnswers.",
     *     operationId="getPossibleAnswers",
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
     *         description="Display a listing of PossibleAnswer.",
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/PossibleAnswer")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity. Undefined method or relationship."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error."
     *     )
     * )
     */

    /**
     * @OA\Get(
     *     path="/possibleAnswers/{possibleAnswerId}",
     *     tags={"PossibleAnswer"},
     *     summary="Find a PossibleAnswer by ID.",
     *     description="Returns a single PossibleAnswer.",
     *     operationId="getPossibleAnswerById",
     *     @OA\Parameter(
     *         name="possibleAnswerId",
     *         in="path",
     *         description="ID of PossibleAnswer to return",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
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
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/PossibleAnswer")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity. Undefined method or relationship."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error."
     *     )
     * )
     */

    /**
     * @OA\Post(
     *     path="/possibleAnswers",
     *     tags={"PossibleAnswer"},
     *     summary="Add a new PossibleAnswer",
     *     operationId="addPossibleAnswer",
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(ref="#/components/schemas/PossibleAnswer")
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Invalid input"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error."
     *     ),
     *     @OA\RequestBody(
     *         description="Create a PossibleAnswer object",
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="published",
     *                 description="Create a published value",
     *                 type="boolean",
     *                 default=true
     *             ),
     *             @OA\Property(
     *                 property="format",
     *                 description="Updated level value",
     *                 type="string",
     *                 enum={"text", "image", "video"},
     *                 default="text"
     *             ),
     *             @OA\Property(
     *                 property="active_fr",
     *                 description="Create a active_fr value",
     *                 type="boolean",
     *                 default=true
     *             ),
     *             @OA\Property(
     *                 property="text_fr",
     *                 description="Create a text_fr value",
     *                 type="string",
     *                 example="un texte par défaut"
     *             ),
     *             @OA\Property(
     *                 property="description_fr",
     *                 description="Create a description_fr value",
     *                 type="string",
     *                 example="une description par défaut"
     *             ),
     *             @OA\Property(
     *                 property="active_en",
     *                 description="Updated active_en value",
     *                 type="boolean",
     *                 default=true
     *             ),
     *             @OA\Property(
     *                 property="text_en",
     *                 description="Create a text_en value",
     *                 type="string",
     *                 example="a default text"
     *             ),
     *             @OA\Property(
     *                 property="description_en",
     *                 description="Create a description_en value",
     *                 type="string",
     *                 example="a default description"
     *             ),
     *             @OA\Property(
     *                 property="position",
     *                 description="Updated position value",
     *                 type="integer",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="question_id",
     *                 description="Create a question_id value",
     *                 type="integer",
     *                 example=1
     *             )
     *         )
     *     )
     * )
     */

    /**
     * @OA\Patch(
     *     path="/possibleAnswers/{possibleAnswersId}",
     *     tags={"PossibleAnswer"},
     *     summary="Update PossibleAnswer by ID",
     *     description="Update a single PossibleAnswer.",
     *     operationId="updatePossibleAnswer",
     *     @OA\Parameter(
     *         name="possibleAnswerId",
     *         in="path",
     *         description="ID of PossibleAnswer that to be updated",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
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
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/PossibleAnswer")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity. Undefined method or relationship."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error."
     *     ),
     *     @OA\RequestBody(
     *         description="Updated PossibleAnswer object",
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="published",
     *                 description="Updated published value",
     *                 type="boolean",
     *                 default=true
     *             ),
     *             @OA\Property(
     *                 property="format",
     *                 description="Updated level value",
     *                 type="string",
     *                 enum={"text", "image", "video"},
     *                 default="text"
     *             ),
     *             @OA\Property(
     *                 property="active",
     *                 description="Updated active value",
     *                 type="boolean",
     *                 default=true
     *             ),
     *             @OA\Property(
     *                 property="text",
     *                 description="Updated text value",
     *                 type="string",
     *                 example="un texte par défaut"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 description="Updated description value",
     *                 type="string",
     *                 example="une description par défaut"
     *             ),
     *             @OA\Property(
     *                 property="position",
     *                 description="Updated position value",
     *                 type="integer",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="question_id",
     *                 description="Create a question_id value",
     *                 type="integer",
     *                 example=1
     *             )
     *         )
     *     )
     * )
     */

    /**
     * @OA\Delete(
     *     path="/possibleAnswer/{possibleAnswerId}",
     *     tags={"PossibleAnswer"},
     *     summary="Delete PossibleAnswer by ID.",
     *     description="Delete a single PossibleAnswer.",
     *     operationId="deletePossibleAnswerById",
     *     @OA\Parameter(
     *         name="possibleAnswerId",
     *         in="path",
     *         description="ID of PossibleAnswer that needs to be deleted",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="successful operation",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity. Undefined method or relationship."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error."
     *     )
     * )
     */
}

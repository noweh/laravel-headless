<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\CourseRepositoryInterface;
use App\Services\Validation\CourseValidator;

class CourseController extends AbstractController
{
    public function __construct(
        CourseRepositoryInterface $repository,
        CourseValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;

        parent::__construct();
    }

    /**
     * @OA\Get(
     *     path="/courses",
     *     tags={"Course"},
     *     summary="Retrieve a listing of Courses.",
     *     description="Returns a listing of Courses.",
     *     operationId="getCourses",
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
     *         name="include",
     *         required=false,
     *         in="query",
     *         description="Include relationship in results. String=themes",
     *         @OA\Schema(
     *             type="string"
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
     *         description="Display a listing of Courses.",
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Course")
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
     *     path="/courses/{courseId}",
     *     tags={"Course"},
     *     summary="Find a Course by ID.",
     *     description="Returns a single Course.",
     *     operationId="getCourseById",
     *     @OA\Parameter(
     *         name="courseId",
     *         in="path",
     *         description="ID of Course to return",
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
     *     @OA\Parameter(
     *         name="include",
     *         required=false,
     *         in="query",
     *         description="Include relationship in results. String=themes",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Course")
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
     *     path="/courses",
     *     tags={"Course"},
     *     summary="Add a new Course",
     *     operationId="addCourse",
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(ref="#/components/schemas/Course")
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
     *         description="Create a Course object",
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
     *                 description="Create a format value",
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
     *                 property="title_fr",
     *                 description="Create a title_fr value",
     *                 type="string",
     *                 example="un titre par défaut"
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
     *                 property="title_en",
     *                 description="Create a title_en value",
     *                 type="string",
     *                 example="a default title"
     *             ),
     *             @OA\Property(
     *                 property="description_en",
     *                 description="Create a description_en value",
     *                 type="string",
     *                 example="a default description"
     *             ),
     *             @OA\Property(
     *                 property="themes_id",
     *                 description="Create a themes_id values",
     *                 type="array",
     *                 @OA\Items(
     *                     type="integer"
     *                 ),
     *                 example={1,2}
     *             )
     *         )
     *     )
     * )
     */

    /**
     * @OA\Patch(
     *     path="/courses/{courseId}",
     *     tags={"Course"},
     *     summary="Update Course by ID",
     *     description="Update a single Course.",
     *     operationId="updateCourse",
     *     @OA\Parameter(
     *         name="courseId",
     *         in="path",
     *         description="ID of Course that to be updated",
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
     *         @OA\JsonContent(ref="#/components/schemas/Course")
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
     *         description="Updated Course object",
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
     *                 property="title",
     *                 description="Updated title value",
     *                 type="string",
     *                 example="un titre par défaut"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 description="Updated description value",
     *                 type="string",
     *                 example="une description par défaut"
     *             ),
     *             @OA\Property(
     *                 property="themes_id",
     *                 description="Updated themes_id values",
     *                 type="array",
     *                 @OA\Items(
     *                     type="integer"
     *                 ),
     *                 example={1,2}
     *             )
     *         )
     *     )
     * )
     */

    /**
     * @OA\Delete(
     *     path="/courses/{courseId}",
     *     tags={"Course"},
     *     summary="Delete Course by ID.",
     *     description="Delete a single Course.",
     *     operationId="deleteCourseById",
     *     @OA\Parameter(
     *         name="courseId",
     *         in="path",
     *         description="ID of Course that needs to be deleted",
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

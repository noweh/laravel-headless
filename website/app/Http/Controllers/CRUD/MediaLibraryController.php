<?php

namespace App\Http\Controllers\CRUD;

use App\Exceptions\ValidationException;
use App\Http\Controllers\CRUD\Traits\CloudinaryTrait;
use App\Repositories\MediaLibraryRepository;
use App\Services\Validators\MediaLibraryValidator;
use Illuminate\Http\Request;

class MediaLibraryController extends AbstractCRUDController
{
    use CloudinaryTrait;

    /**
     * MediaLibraryController constructor.
     * @param MediaLibraryRepository $repository
     * @param MediaLibraryValidator $validator
     */
    public function __construct(
        MediaLibraryRepository $repository,
        MediaLibraryValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;

        parent::__construct();
    }

    /**
     * @OA\Get(
     *     path="/mediaLibraries",
     *     tags={"CRUD\MediaLibrary"},
     *     summary="Retrieve a listing of MediaLibraries.",
     *     description="Returns a listing of MediaLibraries.",
     *     operationId="getMediaLibraries",
     *     @OA\Parameter(
     *         name="lang",
     *         required=false,
     *         in="query",
     *         description="Language",
     *         @OA\Schema(
     *             type="string",
     *             enum={"fr","en"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="mode",
     *         required=false,
     *         in="query",
     *         description="Display mode",
     *         @OA\Schema(
     *             type="string",
     *             enum={"contribution"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="include",
     *         required=false,
     *         in="query",
     *         description="Include relationship in results. String=tags",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="excludeIds",
     *         required=false,
     *         in="query",
     *         description="Exclude ids in results. Multiple values possible, Separated by a comma",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="fUrl",
     *         required=false,
     *         in="query",
     *         description="Filter on urls. Multiple values possible, Separated by a comma",
     *         @OA\Schema(
     *             type="boolean",
     *             enum={true,false}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="fTitle",
     *         required=false,
     *         in="query",
     *         description="Filter on titles. Multiple values possible, Separated by a comma",
     *         @OA\Schema(
     *             type="string"
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
     *         name="relSortByQuantity",
     *         required=false,
     *         in="query",
     *         description="Name of relationship to ORDER BY quantity",
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
     *         description="Display a listing of MediaLibraries.",
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/MediaLibrary")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found"
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
     *     path="/mediaLibraries/{mediaLibraryId}",
     *     tags={"CRUD\MediaLibrary"},
     *     summary="Find a MediaLibrary by ID.",
     *     description="Returns a single MediaLibrary.",
     *     operationId="getMediaLibraryById",
     *     @OA\Parameter(
     *         name="mediaLibraryId",
     *         in="path",
     *         description="ID of MediaLibrary to return",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="lang",
     *         required=false,
     *         in="query",
     *         description="Language",
     *         @OA\Schema(
     *             type="string",
     *             enum={"fr","en"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="mode",
     *         required=false,
     *         in="query",
     *         description="Display mode",
     *         @OA\Schema(
     *             type="string",
     *             enum={"contribution"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="include",
     *         required=false,
     *         in="query",
     *         description="Include relationship in results. String=tags",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation.",
     *         @OA\JsonContent(ref="#/components/schemas/MediaLibrary")
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
     *     path="/mediaLibraries/upload",
     *     tags={"CRUD\MediaLibrary"},
     *     summary="Upload a new MediaLibrary",
     *     operationId="uploadMediaLibrary",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=201,
     *         description="Created.",
     *         @OA\JsonContent(ref="#/components/schemas/AudioLibrary")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized."
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Invalid input."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error."
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="file to upload",
     *                     property="file",
     *                     type="string",
     *                     format="binary",
     *                 ),
     *                 required={"file"}
     *             )
     *         )
     *     )
     * )
     */

    /**
     * @OA\Patch(
     *     path="/mediaLibraries/{mediaLibraryId}",
     *     tags={"CRUD\MediaLibrary"},
     *     summary="Update MediaLibrary by ID",
     *     description="Update a single MediaLibrary.",
     *     operationId="updateMediaLibrary",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="mediaLibraryId",
     *         in="path",
     *         description="ID of MediaLibrary that to be updated",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation.",
     *         @OA\JsonContent(ref="#/components/schemas/MediaLibrary")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized."
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
     *         description="Updated AudioLibrary object",
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="internal_title",
     *                 description="Create an internal title value",
     *                 type="string",
     *                 example="Un titre interne par défaut"
     *             ),
     *             @OA\Property(
     *                 property="artist",
     *                 description="Create an artist name value",
     *                 type="string",
     *                 example="Un nom d'artiste par défaut"
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
     *                 property="primaryTags_id",
     *                 description="Updated primaryTags_id values",
     *                 type="array",
     *                 @OA\Items(
     *                     type="integer"
     *                 ),
     *                 example={1,2}
     *             ),
     *             @OA\Property(
     *                 property="secondaryTags_id",
     *                 description="Updated secondaryTags_id values",
     *                 type="array",
     *                 @OA\Items(
     *                     type="integer"
     *                 ),
     *                 example={3,4}
     *             )
     *         )
     *     )
     * )
     * @param Request $request
     * @param $itemId
     * @return \Illuminate\Http\Resources\Json\Resource
     * @throws ValidationException
     */
    public function update(Request $request, $itemId)
    {
        if ($request->get('url') || $request->get('public_id') || $request->get('width') || $request->get('height')) {
            throw new ValidationException(null, 'fields: url, public_id, width and height can not be updated');
        }

        return parent::update($request, $itemId);
    }

    /**
     * @OA\Delete(
     *     path="/mediaLibraries/{mediaLibraryId}",
     *     tags={"CRUD\MediaLibrary"},
     *     summary="Delete MediaLibrary by ID.",
     *     description="Delete a single MediaLibrary.",
     *     operationId="deleteMediaLibraryById",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="mediaLibraryId",
     *         in="path",
     *         description="ID of MediaLibrary that needs to be deleted",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Successful operation.",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized."
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

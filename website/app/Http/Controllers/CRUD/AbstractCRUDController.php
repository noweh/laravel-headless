<?php

namespace App\Http\Controllers\CRUD;

use App\Exceptions\ValidationException;
use App\Http\Controllers\AbstractController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class AbstractCRUDController extends AbstractController
{
    protected $guard = 'api';
    
    public function __construct()
    {
        auth()->setDefaultDriver('api');
        $this->claim = 'admin';
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function index()
    {
        if ($this->nbPerPage == -1) {
            return formatJsonWithHeaders(
                $this->getResource()::collection(
                    $this->getRepository()->getCollection(
                        $this->getScopeFilters(),
                        $this->getScopeQueries(),
                        $this->parseIncludes(),
                        $this->getOrderFilters(),
                        $this->getExcludeIdsFilters(),
                        $this->getRelationshipOrderByQuantityFilters()
                    )
                )
            );
        } else {
            return formatJsonWithHeaders(
                $this->getResource()::collection(
                    $this->getRepository()->getPaginateCollection(
                        $this->nbPerPage,
                        $this->page,
                        $this->getScopeFilters(),
                        $this->getScopeQueries(),
                        $this->parseIncludes(),
                        $this->getOrderFilters(),
                        $this->getExcludeIdsFilters(),
                        $this->getRelationshipOrderByQuantityFilters()
                    )
                )
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $itemId
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function show()
    {
        $route = \Route::current();
        return formatJsonWithHeaders(
            $this->getResource()::make(
                $this->getRepository()->getByIdWithScope(
                    last($route->parameters()),
                    $this->getScopeFilters(),
                    $this->getScopeQueries(),
                    $this->parseIncludes()
                )
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ValidationException
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json(['data' => $this->getResource()::make(
            $this->doStoreObject($request)
        )], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request): \Illuminate\Http\JsonResponse
    {
        $route = \Route::current();

        return response()->json(
            ['data' => $this->getResource()::make(
                $this->doUpdateObject($request, last($route->parameters()))
            )]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $itemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($itemId): \Illuminate\Http\JsonResponse
    {
        $this->getRepository()->delete($itemId);

        return response()->json(null, 204);
    }

    /**
     * @param Request $request
     * @return Model|\stdClass|array Which is normally a Model.|null
     * @throws ValidationException
     */
    protected function doStoreObject(Request $request)
    {
        if (!$request->request->all()) {
            throw new ValidationException(null, 'No data in input or invalid format for data');
        }
        $input = $this->updateInputBeforeSave($this->getElementsFromRequest($request));
        // If a validator is setted, check if input is validate
        if (!$this->validator || $this->validator->validate($input)) {
            return $this->getRepository()->create($input);
        }

        return null;
    }

    /**
     * @param Request $request
     * @param $itemId
     * @return Model|\stdClass|array Which is normally a Model.|null
     * @throws ValidationException
     */
    protected function doUpdateObject(Request $request, $itemId)
    {
        if (!$request->request->all()) {
            throw new ValidationException(null, 'No data in input or invalid format for data');
        }

        $dataElementsCandidate = $this->getResource()::make(
            $this->getRepository()->getById($itemId, $this->parseIncludes())
        )->toArray([]);
        if ($dataElementsCandidate instanceof \stdClass) {
            $dataElementsCandidate = (array)$dataElementsCandidate;
        }
        $existingData = $this->getElementsFromData($dataElementsCandidate);

        $input = $this->updateInputBeforeSave(
            $this->getElementsFromRequest($request),
            $existingData
        );

        // If a validator is setted, check if existingData + input are validate
        if (!$this->validator || $this->validator->validate(array_merge($existingData, $input))) {
            return $this->getRepository()->update($itemId, $input);
        }

        return null;
    }
}

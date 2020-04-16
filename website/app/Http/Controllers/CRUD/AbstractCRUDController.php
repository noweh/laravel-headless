<?php

namespace App\Http\Controllers\CRUD;

use App\Exceptions\ValidationException;
use App\Http\Controllers\AbstractController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Http\Request;

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
     * @return \Illuminate\Http\JsonResponse
     * @throws ModelNotFoundException
     * @throws RelationNotFoundException
     */
    public function index()
    {
        // If slug or id is setted in url, show a simple resource with the data, else, show the entity collection
        if ($this->id) {
            return $this->show($this->id);
        } elseif ($this->slug) {
            $filteredCollection = $this->getRepository()->getCollection(
                $this->getScopeFilters(),
                $this->getScopeQueries(),
                $this->parseIncludes(),
                $this->getOrderFilters(),
                $this->getExcludeIdsFilters(),
                $this->getRelationshipOrderByQuantityFilters()
            )->filter(function ($item) {
                foreach ($item->getSlugs() as $slug) {
                    if ($slug->slug == $this->slug) {
                        return $item;
                    }
                }
            });

            if ($filteredCollection->isEmpty()) {
                $exception = new ModelNotFoundException();
                $exception->setModel(get_class($this->getRepository()->getModel()));
                throw $exception;
            };

            return response()->json(['data' => $this->getResource()::make(
                $filteredCollection->first()
            )]);
        } else {
            if ($this->nbPerPage == -1) {
                return response()->json(['data' => $this->getResource()::collection(
                    $this->getRepository()->getCollection(
                        $this->getScopeFilters(),
                        $this->getScopeQueries(),
                        $this->parseIncludes(),
                        $this->getOrderFilters(),
                        $this->getExcludeIdsFilters(),
                        $this->getRelationshipOrderByQuantityFilters()
                    )
                )]);
            } else {
                return $this->getResource()::collection(
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
                );
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $itemId
     * @return \Illuminate\Http\JsonResponse
     * @throws ModelNotFoundException
     */
    public function show($itemId)
    {
        return response()->json(
            ['data' => $this->getResource()::make($this->getRepository()->getById($itemId, $this->parseIncludes()))]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ValidationException
     */
    public function store(Request $request)
    {
        $newItem = $this->doStoreObject($request);
        return response()->json(['data' => $this->getResource()::make($newItem)], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param int $itemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $itemId)
    {
        $item = $this->doUpdateObject($request, $itemId);
        return $this->show($item->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $itemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($itemId)
    {
        $this->getRepository()->delete($itemId);

        return response()->json(null, 204);
    }

    protected function doStoreObject(Request $request)
    {
        if (!$request->request->all()) {
            throw new ValidationException(null, 'No data in input or invalid format for data');
        }
        $input = $this->updateInputBeforeSave($this->getElementsFromRequest($request));
        // If a validator is setted, check if input is validate
        if (!$this->validator || $this->validator->validate($input)) {
            $item = $this->getRepository()->create($input);
            return $this->getRepository()->getById($item->id);
        }

        return null;
    }

    protected function doUpdateObject(Request $request, $itemId)
    {
        $dataElementsCandidate = $this->getResource()::make(
            $this->getRepository()->getById($itemId, $this->parseIncludes()))->toArray([]);
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
            $this->getRepository()->update($itemId, $input);
            return $this->getRepository()->getById($itemId);
        }

        return null;
    }
}

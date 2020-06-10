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
        // If slug setted in url, show a simple resource with the data, else, show the entity collection
        if ($this->slug) {
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
    public function store(Request $request)
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
    public function update(Request $request)
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
    public function destroy($itemId)
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
            $item = $this->getRepository()->create($input);
            return $this->getRepository()->getById($item->{$item->getKeyName()});
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
            $this->getRepository()->update($itemId, $input);
            return $this->getRepository()->getById($itemId);
        }

        return null;
    }

    /**
     * Put files in public folders
     * @param $itemId
     * @param Request $request
     * @return Model|\stdClass|array Which is normally a Model.
     */
    protected function checkFilesToUpload($itemId, Request $request)
    {
        ini_set('max_execution_time', '300');
        ini_set('memory_limit', '512M');

        $path = explode('\\', get_class($this->getRepository()->getModel()));
        $storage = \Storage::disk(Str::plural(mb_strtolower(array_pop($path))));

        // if not exists, create folder in public/medias/PATHNAME
        $dirCandidate = $storage->getDriver()->getAdapter()->getPathPrefix() . $itemId;
        if (!$storage->exists($itemId)) {
            //creates directories
            $storage->makeDirectory('/' . $itemId);
            chmod( $dirCandidate, 0777);
        }

        foreach ($request->files as $key => $file) {

            $fileName = $itemId . '/' . $file->getClientOriginalName();

            if ($file && $fileName &&
                $storage->put($fileName, fopen($file->getFileInfo()->getPathname(), 'rb+'))
            ) {
                chmod($storage->getDriver()->getAdapter()->getPathPrefix() . $fileName, 0666);
                $this->getRepository()->update($itemId, [$key . '_url' => $storage->url($fileName) . '?uploaded=1']);
            }
        }

        return $this->getRepository()->getById($itemId);
    }
}

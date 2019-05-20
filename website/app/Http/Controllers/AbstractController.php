<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App;
use Illuminate\Support\Facades\Input;

/**
 * Class AbstractController
 * @package App\Http\Controllers
 *
 * @OA\Info(
 *     version="1.0",
 *     title="API",
 * )
 *
 * @OA\Server(
 *     description="API V1",
 *     url=API_BASE
 * )
 *
 * @OA\Tag(
 *     name="Course",
 *     description="Operations about Courses"
 * )
 *
 * @OA\Tag(
 *     name="PossibleAnswer",
 *     description="Operations about PossibleAnswer"
 * )
 *
 * @OA\Tag(
 *     name="Question",
 *     description="Operations about Question"
 * )
 *
 * @OA\Tag(
 *     name="Questionnaire",
 *     description="Operations about Questionnaires"
 * )
 *
 * @OA\Tag(
 *     name="QuestionType",
 *     description="Operations about QuestionTypes"
 * )
 *
 * @OA\Tag(
 *     name="Session",
 *     description="Operations about Sessions"
 * )
 *
 * @OA\Tag(
 *     name="Theme",
 *     description="Operations about Themes"
 * )
 */
abstract class AbstractController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $resource;
    protected $repository;
    protected $request;
    protected $validator;
    protected $nbPerPage = 25;

    /**
     * AbstractController constructor.
     */
    public function __construct()
    {
        if (request('per_page')) {
            $this->nbPerPage = request('per_page');
        }
    }

    /**
     * Set the Controller's Resource path
     * @param $resource
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
    }

    /**
     * Get the Controller's Resource path
     * @return string
     * @throws ModelNotFoundException
     */
    public function getResource()
    {
        if (!$this->resource) {
            $path = explode('\\', get_class($this));
            $this->resource = 'App\Http\Resources\\' . str_replace('Controller', 'Resource', array_pop($path));
        }

        if (!class_exists($this->resource)) {
            throw new ModelNotFoundException("Resource " . $this->resource . " not found");
        }

        return $this->resource;
    }

    /**
     * @return mixed
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function getScopeFilters(Request $request)
    {
        $scope = [];
        $model = $this->getRepository()->getModel();
        $filters = array_merge($model->getFillable(), $model->getTranslatedAttributes());

        foreach ($filters as $field) {
            if ($request->has($field)) {
                $value = $request->$field;
                if ($value == 'true') {
                    $value = 1;
                }
                if ($value == 'false') {
                    $value = 0;
                }
                if ($value == 0 || !empty($value)) {
                    $scope[$field] = $value;
                }
            }
        }

        return $scope;
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getOrderFilters(Request $request)
    {
        $orders = [];
        if ($request->has('sort') && $request->has('sortOrder')) {
            $orders[$request->get('sort')] = $request->get('sortOrder');
        } elseif ($request->has('sort')) {
            $orders[$request->get('sort')] = 'asc';
        }
        return $orders;
    }

    /**
     * Get all includes to set for the Model Collection
     * @param Request $request
     * @return array
     */
    private function parseIncludes(Request $request)
    {
        return $request->get('include') ? explode(',', $request->get('include')) : [];
    }

    /**
     * Retrieve all elements from Request and set translatedElements
     * @param Request $request
     * @return array
     */
    private function getElementsFromRequest(Request $request)
    {
        $elementsInRequest = $request->request->all();

        $reconstructedElements = [];
        foreach ($elementsInRequest as $elementKeyInRequest => $elementValueInRequest) {
            if (in_array($elementKeyInRequest, $this->getRepository()->getModel()->getTranslatedAttributes())) {
                $reconstructedElements[$elementKeyInRequest . '_' . App::getLocale()] = $elementValueInRequest;
            } else {
                $reconstructedElements[$elementKeyInRequest] = $elementValueInRequest;
            }
        }
        return $reconstructedElements;
    }

    /**
     * Display a listing of the resource.
     * ex : http://academy.operadeparis.backstage.test/api/v1/themes?lang=fr&include=sessions&label=%%lu%%&sort=id&sortOrder=desc
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        return $this->getResource()::collection(
            $this->getRepository()->getPaginateCollection(
                $this->nbPerPage,
                $this->getScopeFilters($request),
                $this->parseIncludes($request),
                $this->getOrderFilters($request)
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $itemId
     * @return mixed
     */
    public function show(Request $request, $itemId)
    {
        return $this->getResource()::make($this->getRepository()->getById($itemId, $this->parseIncludes($request)));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $input = $this->getElementsFromRequest($request);

        // If a validator is setted, check if input is validate
        if (!$this->validator || $this->validator->validate($input)) {
            return response()->json($this->getResource()::make(
                $this->getRepository()->create($this->getElementsFromRequest($request))
            ), 201);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param int $itemId
     * @return mixed
     */
    public function update(Request $request, $itemId)
    {
        $this->getRepository()->update($itemId, $this->getElementsFromRequest($request));

        return $this->show($request, $itemId);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $itemId
     * @return \Illuminate\Http\Response
     */
    public function destroy($itemId)
    {
        $this->getRepository()->getById($itemId)->delete();

        return response()->json(null, 204);
    }
}

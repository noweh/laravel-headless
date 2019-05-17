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
     * @return array
     */
    protected function getScopeFilters()
    {
        $scope = [];
        $model = $this->getRepository()->getModel();
        $filters = array_merge($model->getFillable(), $model->getTranslatedAttributes());

        foreach ($filters as $field) {
            if ($this->request->has($field)) {
                $value = $this->request->$field;
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
     * @return array
     */
    private function getOrderFilters()
    {
        $orders = [];
        if ($this->request->has('sort') && $this->request->has('sortOrder')) {
            $orders[$this->request->get('sort')] = $this->request->get('sortOrder');
        } elseif ($this->request->has('sort')) {
            $orders[$this->request->get('sort')] = 'asc';
        }
        return $orders;
    }

    /**
     * Get all includes to set for the Model Collection
     * @return array
     */
    private function parseIncludes()
    {
        return $this->request->get('include') ? explode(',', $this->request->get('include')) : [];
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
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return $this->getResource()::collection(
            $this->getRepository()->getPaginateCollection(
                $this->nbPerPage,
                $this->getScopeFilters(),
                $this->parseIncludes(),
                $this->getOrderFilters()
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param $itemId
     * @return mixed
     */
    public function show($itemId)
    {
        return $this->getResource()::make($this->getRepository()->getById($itemId, $this->parseIncludes()));
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
        $validateData = $this->validator->validate($input);
        die("okk");
        $item = $this->getRepository()->create($this->getElementsFromRequest($request));
        return response()->json($this->getResource()::make($item), 201);
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

        return $this->show($itemId);
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

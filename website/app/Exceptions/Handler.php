<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Config;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  Exception  $exception
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|Response
     */
    public function render($request, Exception $exception)
    {
        if (!$exception instanceof NotFoundHttpException) {
            if (method_exists($exception, 'getStatusCode') &&
                $exception->getStatusCode() == Response::HTTP_METHOD_NOT_ALLOWED
            ) {
                abort(Response::HTTP_NOT_FOUND);
            }

            if ($request->wantsJson() || $request->is('api*')) {
                if ($exception instanceof RelationNotFoundException ||
                    $exception instanceof ValidationException
                ) {
                    return response()->json(['error' => $exception->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
                } elseif ($exception instanceof ModelNotFoundException) {
                    return response()->json(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND);
                } elseif ($exception instanceof AuthenticationException) {
                    return response()->json(['error' => $exception->getMessage()], Response::HTTP_UNAUTHORIZED);
                } elseif ($exception instanceof LockedException) {
                    return response()->json(['error' => $exception->getMessage()], Response::HTTP_LOCKED);
                } else {
                    if (Config::get('app.debug') && $exception->getMessage()) {
                        return response()->json(
                            [
                                'error' => $exception->getMessage(),
                                'file' => $exception->getFile(),
                                'line' => $exception->getLine()
                            ],
                            (method_exists($exception, 'getStatusCode')) ?
                                $exception->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR
                        );
                    } else {
                        return response()->json(
                            null,
                            (method_exists($exception, 'getStatusCode')) ?
                                $exception->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR
                        );
                    }
                }
            }
        }

        return parent::render($request, $exception);
    }
}

<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
     * @param  \Exception $exception
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof ModelNotFoundException || $exception instanceof RelationNotFoundException) {
            return response()->json(['error' => $exception->getMessage()], 422);
        } elseif ($exception instanceof Exception) {
            if ($exception->getMessage()) {
                return response()->json(
                    [
                        'error' => $exception->getMessage(),
                        'file' => $exception->getFile(),
                        'line' => $exception->getLine()
                    ],
                    (method_exists($exception, 'getStatusCode')) ? $exception->getStatusCode() : '500'
                );
            } else {
                return response()->json(null, $exception->getStatusCode());
            }
        }

        return parent::render($request, $exception);
    }
}

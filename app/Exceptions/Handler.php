<?php

namespace App\Exceptions;

use App\Exceptions\Setting\TypeException;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
     * @param  \Exception  $exception
     * @return void
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
        if($exception instanceof AuthorizationException) {
            return response()->view('errors.errors', [
                'title'   => 'Forbidden',
                'message' => 'You have no access to this page.'
            ], 403);
        }else if($exception instanceof TypeException) {
            return response()->json([
                'status'  => $exception->getCode(),
                'message' => $exception->getMessage(),
            ]);
        }

        return parent::render($request, $exception);
    }
}

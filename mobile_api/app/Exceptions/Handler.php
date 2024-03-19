<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Arr;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    public function render($request, Throwable $e)
    {
        if ($e instanceof ModelNotFoundException) {
            $replacement = [
                'id' => collect($e->getIds())->first(),
                'model' => Arr::last(explode('\\', $e->getModel())),
            ];

        
        $response = [
            'success' => false,
            'data' => null,
            'message' => 'Data not Found.',
            ];
            return response()->json($response , 400);
        }

        return parent::render($request, $e);
    }
}

<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Http\Request;
use App\Exceptions\InvalidOrderException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        //response to send when exception occurs
        $response = [
            'success' => false,
            'error' => '',
            'data' => [],
            'errors' => [],
        ];

        $this->renderable(function (InvalidOrderException $e, Request $request) use ($response) {
            if ($request->is('api/*')) {
                $response['error'] = __('message.exceptions.server_error');
                return response()->json($response, HTTP_INTERNAL_SERVER_ERROR);
            }
        });

        $this->renderable(function (NotFoundHttpException $e, Request $request) use ($response) {
            if ($request->is('api/*')) {
                $response['error'] = __('message.exceptions.not_found');
                return response()->json($response, HTTP_NOT_FOUND);
            }
        });
    }
}

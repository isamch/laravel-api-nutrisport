<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                return $this->handleApiException($e);
            }
        });
    }

    protected function handleApiException(Throwable $e)
    {
        $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
        
        if ($statusCode < 100 || $statusCode >= 600) {
            $statusCode = 500;
        }

        return response()->json([
            'success' => false,
            'message' => $e->getMessage() ?: 'Server Error',
            'error' => config('app.debug') ? [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ] : null,
        ], $statusCode);
    }
}

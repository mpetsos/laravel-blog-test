<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class ApiHandler
{
    public static function render(Throwable $exception, $request): ?JsonResponse
    {
        if ($request->is('api/*') || $request->expectsJson()) {
            return self::handle($exception);
        }

        return null;
    }

    private static function handle(Throwable $exception): JsonResponse
    {
        $statusCode = self::getStatusCode($exception);
        $message = self::getMessage($exception, $statusCode);

        $response = [
            'success' => false,
            'message' => $message,
            'status' => $statusCode,
        ];

        // Add validation errors
        if ($exception instanceof ValidationException) {
            $response['errors'] = $exception->errors();
        }

        // Add debug info
        if (config('app.debug')) {
            $response['debug'] = [
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTrace()
            ];
        }

        return response()->json($response, $statusCode);
    }

    private static function getStatusCode(Throwable $exception): int
    {
        if (method_exists($exception, 'getStatusCode')) {
            return $exception->getStatusCode();
        }

        if ($exception instanceof ValidationException) {
            return 422;
        }

        if ($exception instanceof AuthenticationException) {
            return 401;
        }

        if ($exception instanceof NotFoundHttpException) {
            return 404;
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return 405;
        }

        return 500;
    }

    private static function getMessage(Throwable $exception, int $statusCode): string
    {
        // Return the exception message if it's not empty
        if (!empty($exception->getMessage())) {
            return $exception->getMessage();
        }

        // Default messages based on status code
        return match($statusCode) {
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Resource not found',
            405 => 'Method not allowed',
            422 => 'Validation failed',
            500 => config('app.debug') ? $exception->getMessage() : 'Server Error',
            default => 'An error occurred',
        };
    }
}
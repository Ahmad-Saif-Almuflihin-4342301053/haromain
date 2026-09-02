<?php

use App\Http\Middleware\AuthorizeUser;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->group('api', [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'custom' => AuthorizeUser::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                // Handle validation exceptions
                if ($e instanceof ValidationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $e->errors(),
                    ], 422);
                }

                // Handle HTTP exceptions (e.g., 404, 403, 405, etc.)
                if ($e instanceof HttpExceptionInterface) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage() ?: 'HTTP error',
                    ], $e->getStatusCode());
                }

                if ($e->getMessage() == 'Unauthenticated.' || $e->getMessage() == 'Unauthenticated') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized',
                    ], 403);
                }
                // Default for all other exceptions
                return response()->json([
                    'success' => false,
                    'message' => 'Server Error',
                    'errors' => $e->getMessage(),
                ], 500);
            }

            // Let Laravel handle non-API requests normally
            return null;
        });
        $exceptions->render(function (HttpException $exception, Request $request) {
            $statusCode = $exception->getStatusCode();

            /* if ($statusCode == 400) {
                return response()->view("errors.400", [], 400);
            }
            */
            // if ($statusCode == 403) {
            //     return response()->view("errors.403", [], 403);
            // }
    
            // if ($statusCode == 404) {
            //     return response()->view("errors.404", [], 404);
            // }
    
            /* if ($statusCode == 500) {
                return response()->view("errors.500", [], 500);
            }

            if ($statusCode == 503) {
                return response()->view("errors.503", [], 503);
            } */

            //return response()->view('errors.default', [], $statusCode);
            // return response()->view('errors.500', [], $statusCode);
        });
    })->create();

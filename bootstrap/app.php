<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo('/admin/login');
        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                if ($e instanceof ValidationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed.',
                        'errors' => $e->errors(),
                    ], 422);
                }

                if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Resource not found.',
                    ], 404);
                }

                if ($e instanceof AuthenticationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthenticated.',
                    ], 401);
                }

                if ($e instanceof AuthorizationException || $e instanceof AccessDeniedHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized access.',
                    ], 403);
                }

                if ($e instanceof ConflictHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage() ?: 'Conflict detected.',
                    ], 409);
                }

                if ($e instanceof MethodNotAllowedHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Method not allowed.',
                    ], 405);
                }

                if ($e instanceof HttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage() ?: 'HTTP error occurred.',
                    ], $e->getStatusCode());
                }

                // Generic unexpected exceptions - never expose stack trace or database errors
                return response()->json([
                    'success' => false,
                    'message' => 'An unexpected error occurred. Please try again later.',
                ], 500);
            }

            return null;
        });
    })->create();

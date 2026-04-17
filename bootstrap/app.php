<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        // Sanctum API stateful middleware
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        // IMPORTANTE: evita redirect para login
        $middleware->redirectGuestsTo(null);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Validation error',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        // $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
        //     if ($request->is('api/*')) {
        //         return response()->json([
        //             'message' => 'Resource not found'
        //         ], 404);
        //     }
        // });

        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('api/*')) {

                $status = $e instanceof HttpExceptionInterface
                    ? $e->getStatusCode()
                    : 500;

                return response()->json([
                    'message' => app()->hasDebugModeEnabled()
                        ? $e->getMessage()
                        : 'Server Error',
                ], $status);
            }
        });

    })
    ->create();

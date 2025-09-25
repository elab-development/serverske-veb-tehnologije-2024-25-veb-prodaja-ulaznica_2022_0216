<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $jsonError = function (\Throwable $e, int $status, string $type, ?array $details = null) {
            $payload = [
                'ok'    => false,
                'error' => [
                    'type'    => $type,
                    'message' => $e->getMessage() ?: \Symfony\Component\HttpFoundation\Response::$statusTexts[$status] ?? 'Error',
                ],
            ];
            if ($details) {
                $payload['error']['details'] = $details;
            }
            return response()->json($payload, $status);
        };

        $exceptions->render(function (\Throwable $e, $request) use ($jsonError) {

            if (! $request->is('api/*')) {
                return null; // za web deo ostaje default
            }

            if ($e instanceof ValidationException) {
                return response()->json([
                    'ok'    => false,
                    'error' => [
                        'type'    => 'validation_error',
                        'message' => 'Validation failed.',
                        'details' => $e->errors(),
                    ],
                ], 422);
            }

            if ($e instanceof AuthenticationException)   return $jsonError($e, 401, 'unauthenticated');
            if ($e instanceof AuthorizationException)    return $jsonError($e, 403, 'forbidden');
            if ($e instanceof ModelNotFoundException)    return $jsonError(new NotFoundHttpException('Resource not found.', $e), 404, 'not_found');
            if ($e instanceof NotFoundHttpException)     return $jsonError($e, 404, 'not_found');
            if ($e instanceof MethodNotAllowedHttpException) return $jsonError($e, 405, 'method_not_allowed');
            if ($e instanceof ThrottleRequestsException) return $jsonError($e, 429, 'too_many_requests');

            if ($e instanceof QueryException) {
                $msg = app()->isProduction() ? 'Database error.' : $e->getMessage();
                return response()->json([
                    'ok'    => false,
                    'error' => [
                        'type'    => 'database_error',
                        'message' => $msg,
                    ],
                ], 500);
            }

            if ($e instanceof HttpExceptionInterface) {
                return $jsonError($e, $e->getStatusCode(), 'http_error');
            }

            $msg = app()->isProduction() ? 'Server error.' : $e->getMessage();
            return response()->json([
                'ok'    => false,
                'error' => [
                    'type'    => 'server_error',
                    'message' => $msg,
                ],
            ], 500);
        });
    })
    ->create();

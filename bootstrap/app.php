<?php

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\Middleware\StartSession;
use App\Http\Middleware\EnsureTokenIsValid;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // If you need custom global middleware:
        // $middleware->append(EnsureTokenIsValid::class);
        // $middleware->append(EnsureUserHasRole::class . ':admin');

        // Web middleware group
        $middleware->web(append: [
            \Illuminate\Http\Middleware\HandleCors::class,
            ValidateCsrfToken::class,
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            AuthenticateSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            HandleInertiaRequests::class,
        ]);

        // API middleware group
        $middleware->api(append: [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // CSRF exceptions
        $middleware->validateCsrfTokens(except: [
            'login/cirms', // <-- excluded route
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // $exceptions->respond(function (Response $response) {
        //     // Intercept the 419 status code, which indicates a CSRF mismatch.
        //     if ($response->getStatusCode() === 419) {
        //         // Return an Inertia redirect to the login page.
        //         // This forces a full page reload, getting a new session and token.
        //         return to_route('login');
        //     }

        //     // For all other exceptions, return the default response.
        //     return $response;
        // });
    })->create();

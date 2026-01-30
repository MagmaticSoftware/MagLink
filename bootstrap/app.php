<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\RedirectIfNotSubscribed;
use App\Http\Middleware\SetDefaultTenantForUrls;
use App\Http\Middleware\ShareTrialData;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
            ShareTrialData::class,
        ]);

        $middleware->alias([
            'tenant' => SetDefaultTenantForUrls::class,
            'subscribed' => RedirectIfNotSubscribed::class,
        ]);

        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response) {
            $status = $response->getStatusCode();
            
            // Render custom error pages for common HTTP errors
            if (in_array($status, [403, 404, 500, 503])) {
                $messages = [
                    403 => 'Non hai i permessi necessari per accedere a questa pagina.',
                    404 => 'La pagina che stai cercando non esiste o è stata spostata.',
                    500 => 'Si è verificato un errore interno del server. Stiamo lavorando per risolverlo.',
                    503 => 'Il servizio è temporaneamente non disponibile. Riprova tra qualche minuto.',
                ];
                
                return \Inertia\Inertia::render('Error', [
                    'status' => $status,
                    'message' => $messages[$status] ?? 'Si è verificato un errore.'
                ])
                ->toResponse(request())
                ->setStatusCode($status);
            }

            return $response;
        });
    })->create();

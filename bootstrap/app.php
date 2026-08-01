<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e) {
            return response(
                "<h1>Exception: " . $e->getMessage() . "</h1>" .
                "<pre>" . $e->getFile() . ":" . $e->getLine() . "\n\n" . $e->getTraceAsString() . "</pre>",
                500
            );
        });
    })->create();

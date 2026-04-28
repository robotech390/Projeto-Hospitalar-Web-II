<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: 'api',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Registra o middleware de autenticação JWT do Grupo 1
        $middleware->alias([
            'auth.jwt' => \App\Http\Middleware\AuthJwt::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Retorna erros 404 e 500 em JSON para uma API REST
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registro não encontrado.',
            ], 404);
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Método HTTP não permitido para esta rota.',
            ], 405);
        });
    })->create();

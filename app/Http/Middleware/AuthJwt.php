<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class AuthJwt
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token não fornecido. Envie no header: Authorization: Bearer {token}',
            ], 401);
        }

        try {
            $response = Http::withToken($token)
                ->timeout(5)
                ->get(config('services.grupo1.url') . '/api/auth/validate');

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token inválido ou expirado.',
                ], 401);
            }

            // Injeta dados do usuário autenticado para uso nos controllers
            $request->merge(['auth_user' => $response->json('data')]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Serviço de autenticação indisponível. Tente novamente.',
            ], 503);
        }

        return $next($request);
    }
}

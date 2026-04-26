<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * AutenticacaoJWT
 *
 * Middleware que protege rotas exigindo um token JWT válido.
 * O token deve ser enviado no header Authorization no formato:
 *   Authorization: Bearer {token}
 *
 * Em caso de falha, retorna JSON padronizado com a causa do erro.
 */
class AutenticacaoJWT
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException) {
            return response()->json([
                'mensagem' => 'Token expirado. Por favor, faça login novamente.',
                'erro'     => 'token_expirado',
            ], 401);
        } catch (TokenInvalidException) {
            return response()->json([
                'mensagem' => 'Token inválido.',
                'erro'     => 'token_invalido',
            ], 401);
        } catch (JWTException) {
            return response()->json([
                'mensagem' => 'Token não fornecido.',
                'erro'     => 'token_ausente',
            ], 401);
        }

        return $next($request);
    }
}

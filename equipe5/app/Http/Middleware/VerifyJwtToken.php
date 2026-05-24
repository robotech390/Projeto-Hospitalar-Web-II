<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyJwtToken
{
    /**
     * Trata uma requisição de entrada.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (empty($token)) {
            Log::warning('VerifyJwtToken: Tentativa de acesso sem Bearer Token.');
            return response()->json([
                'message' => 'Token JWT não fornecido ou inválido no cabeçalho Authorization.'
            ], 401);
        }

        // Gera uma chave de cache única para o token recebido
        $cacheKey = 'jwt_val_' . md5($token);
        
        // Cacheia a validação por 300 segundos (5 minutos) para evitar chamadas excessivas ao microsserviço
        $userData = Cache::remember($cacheKey, 300, function () use ($token) {
            return $this->validateTokenWithAuthService($token);
        });

        if (empty($userData)) {
            // Se o token for inválido, limpa o cache imediatamente (caso tenha guardado um resultado nulo por erro transitório)
            Cache::forget($cacheKey);
            
            return response()->json([
                'message' => 'Token JWT inválido, expirado ou não autorizado pelo serviço central.'
            ], 401);
        }

        // Injeta os dados decodificados do usuário nos atributos do request
        $request->attributes->set('authenticated_user', $userData);

        return $next($request);
    }

    /**
     * Valida o token consumindo o endpoint do microsserviço de autenticação (Equipe 1).
     * Retorna os dados do usuário em caso de sucesso, ou null se for inválido/falhar.
     *
     * @param string $token
     * @return array|null
     */
    protected function validateTokenWithAuthService(string $token): ?array
    {
        $authUrl = config('services.microservices.auth');

        // Fallback robusto se a URL do microsserviço não estiver explicitamente configurada
        if (empty($authUrl)) {
            $authUrl = env('API_URL');
            if (empty($authUrl)) {
                $authUrl = 'http://localhost:8000/api';
            }
        }

        try {
            $baseUrl = rtrim($authUrl, '/');
            
            // Resolve o endpoint com base na estrutura da URL (evita duplicar "/api")
            if (str_ends_with($baseUrl, '/api')) {
                $endpoint = $baseUrl . '/auth/me';
            } else {
                $endpoint = $baseUrl . '/api/auth/me';
            }

            Log::debug("VerifyJwtToken: Enviando requisição de validação para {$endpoint}");

            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout(5)
                ->get($endpoint);

            if ($response->successful()) {
                $data = $response->json();
                
                // Mapeia de forma genérica a estrutura retornada pelo microsserviço
                // Pode vir aninhado em 'user', 'usuario', ou direto na raiz
                $user = $data['user'] ?? $data['usuario'] ?? $data ?? null;

                if (is_array($user)) {
                    Log::info('VerifyJwtToken: Token validado com sucesso para o usuário ID: ' . ($user['id'] ?? 'desconhecido'));
                    return $user;
                }
            }

            Log::warning("VerifyJwtToken: Falha na validação do token. Código de status: " . $response->status() . " - Corpo: " . $response->body());
            return null;

        } catch (\Throwable $e) {
            Log::error("VerifyJwtToken: Erro de comunicação com o serviço de autenticação: " . $e->getMessage());
            return null;
        }
    }
}

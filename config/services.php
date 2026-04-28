<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Grupo 1 — Serviço de Autenticação
    |--------------------------------------------------------------------------
    | URL base da API do Grupo 1. Configure no .env com a variável GRUPO1_URL.
    */

    'grupo1' => [
        'url' => env('GRUPO1_URL', 'http://localhost:8001'),
    ],

    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

];

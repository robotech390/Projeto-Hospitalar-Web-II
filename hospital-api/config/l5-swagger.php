<?php

return [
    'default' => 'default',

    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'Hospital API — Equipe 1',
            ],

            'routes' => [
                /*
                 * Rota para acessar a documentação Swagger UI:
                 * http://seu-servidor/api/documentacao
                 */
                'api' => 'api/documentacao',
            ],

            'paths' => [
                /*
                 * Onde o JSON/YAML gerado será salvo.
                 */
                'docs'         => storage_path('api-docs'),
                'docs_json'    => 'api-docs.json',
                'docs_yaml'    => 'api-docs.yaml',
                'annotations'  => [
                    base_path('app'),
                ],

                'format_to_use_for_docs' => env('L5_FORMAT_TO_USE_FOR_DOCS', 'json'),

                'excludes' => [],
            ],

            'scanOptions' => [
                'analyser'    => null,
                'analysis'    => null,
                'processors'  => [],
                'pattern'     => null,
                'exclude'     => [],
                'open_api_spec_version' => \L5Swagger\Generator::OPEN_API_DEFAULT_SPEC_VERSION,
            ],

            'securityDefinitions' => [
                'securitySchemes' => [],
                'security'        => [],
            ],

            'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', false),

            'generate_yaml_copy' => env('L5_SWAGGER_GENERATE_YAML_COPY', false),

            'proxy' => false,

            'additional_config_url' => null,

            'operations_sort' => env('L5_SWAGGER_OPERATIONS_SORT', null),

            'validator_url' => null,

            'ui' => [
                'display' => [
                    'doc_expansion'    => env('L5_SWAGGER_UI_DOC_EXPANSION', 'none'),
                    'filter'           => env('L5_SWAGGER_UI_FILTERS', true),
                    'show_extensions'  => env('L5_SWAGGER_UI_SHOW_EXTENSIONS', false),
                    'show_common_extensions' => env('L5_SWAGGER_UI_SHOW_COMMON_EXTENSIONS', false),
                    'try_it_out_enabled' => env('L5_SWAGGER_UI_TRY_IT_OUT_ENABLED', true),
                ],
                'authorization' => [
                    'persist_authorization' => env('L5_SWAGGER_UI_PERSIST_AUTHORIZATION', true),
                    'oauth2RedirectUrl'      => env('L5_SWAGGER_OAUTH2_REDIRECT_URL', '/api/oauth2-callback'),
                    'initOAuth' => [
                        'usePkceWithAuthorizationCodeGrant' => false,
                    ],
                ],
            ],

            'constants' => [
                'L5_SWAGGER_CONST_HOST' => env('L5_SWAGGER_CONST_HOST', 'http://localhost:8000'),
            ],
        ],
    ],

    'defaults' => [
        'routes' => [
            'docs'      => 'docs',
            'oauth2_callback' => 'api/oauth2-callback',
            'middleware' => [
                'api'   => [],
                'asset' => [],
                'docs'  => [],
                'oauth2_callback' => [],
            ],
            'group_options' => [],
        ],

        'paths' => [
            'docs'        => storage_path('api-docs'),
            'views'       => base_path('resources/views/vendor/l5-swagger'),
            'base'        => env('L5_SWAGGER_BASE_PATH', null),
            'swagger_ui_assets_path' => env('L5_SWAGGER_UI_ASSETS_PATH', 'vendor/swagger-api/swagger-ui/dist/'),
            'excludes'    => [],
        ],

        'scanOptions' => [
            'analyser'   => null,
            'analysis'   => null,
            'processors' => [],
            'pattern'    => null,
            'exclude'    => [],
            'open_api_spec_version' => \L5Swagger\Generator::OPEN_API_DEFAULT_SPEC_VERSION,
        ],

        'securityDefinitions' => [
            'securitySchemes' => [],
            'security'        => [],
        ],

        'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', false),

        'generate_yaml_copy' => env('L5_SWAGGER_GENERATE_YAML_COPY', false),

        'proxy' => false,

        'additional_config_url' => null,

        'operations_sort' => env('L5_SWAGGER_OPERATIONS_SORT', null),

        'validator_url' => null,

        'ui' => [
            'display' => [
                'doc_expansion'          => env('L5_SWAGGER_UI_DOC_EXPANSION', 'none'),
                'filter'                 => env('L5_SWAGGER_UI_FILTERS', true),
                'show_extensions'        => env('L5_SWAGGER_UI_SHOW_EXTENSIONS', false),
                'show_common_extensions' => env('L5_SWAGGER_UI_SHOW_COMMON_EXTENSIONS', false),
                'try_it_out_enabled'     => env('L5_SWAGGER_UI_TRY_IT_OUT_ENABLED', true),
            ],
            'authorization' => [
                'persist_authorization' => env('L5_SWAGGER_UI_PERSIST_AUTHORIZATION', true),
                'oauth2RedirectUrl'      => env('L5_SWAGGER_OAUTH2_REDIRECT_URL', '/api/oauth2-callback'),
                'initOAuth' => [
                    'usePkceWithAuthorizationCodeGrant' => false,
                ],
            ],
        ],

        'constants' => [
            'L5_SWAGGER_CONST_HOST' => env('L5_SWAGGER_CONST_HOST', 'http://localhost:8000'),
        ],
    ],
];

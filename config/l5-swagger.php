<?php

return [
    'default' => 'default',
    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'L5 Swagger UI',
            ],

            'routes' => [
                /*
                 * Ruta para acceder a la interfaz de documentación de la API
                 */
                'api' => 'api/documentation',  // Ruta donde estará la documentación de la API
            ],
            'paths' => [
                /*
                 * Edita para usar la ruta completa de los recursos de Swagger UI (si es necesario)
                 */
                'use_absolute_path' => env('L5_SWAGGER_USE_ABSOLUTE_PATH', true),

                /*
                 * Ruta donde se almacenarán los archivos de Swagger UI
                 */
                'swagger_ui_assets_path' => env('L5_SWAGGER_UI_ASSETS_PATH', 'vendor/swagger-api/swagger-ui/dist/'),

                /*
                 * Nombre del archivo JSON generado con la documentación
                 */
                'docs_json' => 'api-docs.json',

                /*
                 * Nombre del archivo YAML generado con la documentación
                 */
                'docs_yaml' => 'api-docs.yaml',

                /*
                 * Formato a utilizar para los documentos, 'json' o 'yaml'
                 */
                'format_to_use_for_docs' => env('L5_FORMAT_TO_USE_FOR_DOCS', 'json'),

                /*
                 * Rutas absolutas de directorios donde se almacenan las anotaciones de Swagger
                 */
                'annotations' => [
                    base_path('app/Swagger'),
                    base_path('app/Http/Controllers'),
                ],
            ],
        ],
    ],

    'defaults' => [
        'routes' => [
            /*
             * Ruta para acceder a las anotaciones de Swagger generadas
             */
            'docs' => 'docs',

            /*
             * Ruta para la autenticación OAuth2
             */
            'oauth2_callback' => 'api/oauth2-callback',

            /*
             * Middleware para evitar accesos inesperados a la documentación de la API
             */
            'middleware' => [
                'api' => [],
                'asset' => [],
                'docs' => [],
                'oauth2_callback' => [],
            ],

            /*
             * Opciones del grupo de rutas
             */
            'group_options' => [],
        ],

        'paths' => [
            /*
             * Ruta donde se almacenarán las anotaciones de Swagger procesadas
             */
            'docs' => storage_path('api-docs'),

            /*
             * Ruta donde se exportarán las vistas
             */
            'views' => base_path('resources/views/vendor/l5-swagger'),

            /*
             * Configuración de la base URL de la API
             */
            'base' => env('L5_SWAGGER_BASE_PATH', null),

            /*
             * Carpetas que deben ser excluidas del escaneo
             */
            'excludes' => [],
        ],

        'scanOptions' => [
            /**
             * Configuración de los procesadores predeterminados
             */
            'default_processors_configuration' => [
                /** Ejemplo de configuración */
            ],

            /**
             * Analizador que se utilizará. Por defecto es \OpenApi\StaticAnalyser.
             */
            'analyser' => null,

            /**
             * Análisis predeterminado. Por defecto es \OpenApi\Analysis.
             */
            'analysis' => null,

            /**
             * Procesadores personalizados de la consulta de ruta.
             */
            'processors' => [
                // new \App\SwaggerProcessors\SchemaQueryParameter(),
            ],

            /**
             * Patrón de archivos a escanear. Por defecto es *.php.
             */
            'pattern' => null,

            /*
             * Directorios que deben ser excluidos del escaneo
             */
            'exclude' => [],

            /*
             * Especificación de OpenAPI a generar, por defecto es la versión 3.0.0.
             */
            'open_api_spec_version' => env('L5_SWAGGER_OPEN_API_SPEC_VERSION', \L5Swagger\Generator::OPEN_API_DEFAULT_SPEC_VERSION),
        ],

        /*
         * Definiciones de seguridad de la API
         */
        'securityDefinitions' => [
            'securitySchemes' => [
                /*
                 * Ejemplo de definición de seguridad OAuth2
                 */
                'passport' => [
                    'type' => 'oauth2',
                    'description' => 'Laravel Passport OAuth2 security.',
                    'in' => 'header',
                    'scheme' => 'https',
                    'flows' => [
                        "password" => [
                            "authorizationUrl" => config('app.url') . '/oauth/authorize',
                            "tokenUrl" => config('app.url') . '/oauth/token',
                            "refreshUrl" => config('app.url') . '/token/refresh',
                            "scopes" => []
                        ],
                    ],
                ],
                'sanctum' => [
                    'type' => 'apiKey',
                    'description' => 'Enter token in format (Bearer <token>)',
                    'name' => 'Authorization',
                    'in' => 'header',
                ],
            ],
            'security' => [
                /*
                 * Ejemplo de seguridad a usar
                 */
                [
                    'passport' => []
                ],
            ],
        ],

        /*
         * Configuración para regenerar la documentación siempre que sea necesario
         */
        'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', true),  // Activado para siempre regenerar

        /*
         * Configuración para generar una copia de la documentación en formato YAML
         */
        'generate_yaml_copy' => env('L5_SWAGGER_GENERATE_YAML_COPY', true), // Activado para generar YAML

        /*
         * Si se encuentra detrás de un proxy, puedes agregar la IP de confianza
         */
        'proxy' => false,

        /*
         * Configuración adicional para obtener configuraciones externas
         */
        'additional_config_url' => null,

        /*
         * Configuración para ordenar las operaciones en la UI de Swagger
         */
        'operations_sort' => env('L5_SWAGGER_OPERATIONS_SORT', null),

        /*
         * Validación de la documentación Swagger
         */
        'validator_url' => null,

        /*
         * Configuración de la UI de Swagger
         */
        'ui' => [
            'display' => [
                'dark_mode' => env('L5_SWAGGER_UI_DARK_MODE', false),

                /*
                 * Expansión predeterminada de las operaciones y las etiquetas en la UI
                 */
                'doc_expansion' => env('L5_SWAGGER_UI_DOC_EXPANSION', 'none'),

                /*
                 * Habilitar filtrado en la UI
                 */
                'filter' => env('L5_SWAGGER_UI_FILTERS', true),
            ],

            'authorization' => [
                /*
                 * Si se activa, se guardarán los datos de autorización para que no se pierdan en el navegador
                 */
                'persist_authorization' => env('L5_SWAGGER_UI_PERSIST_AUTHORIZATION', false),

                'oauth2' => [
                    /*
                     * Habilitar PKCE en el flujo de AuthorizationCodeGrant
                     */
                    'use_pkce_with_authorization_code_grant' => false,
                ],
            ],
        ],

        /*
         * Constantes que pueden ser utilizadas en las anotaciones
         */
        'constants' => [
            'L5_SWAGGER_CONST_HOST' => env('L5_SWAGGER_CONST_HOST', 'http://my-default-host.com'),
        ],
    ],
];

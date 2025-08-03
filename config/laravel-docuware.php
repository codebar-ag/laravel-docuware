<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cache driver
    |--------------------------------------------------------------------------
    | You may like to define a different cache driver than the default Laravel cache driver.
    | In Laravel 12+, CACHE_STORE is used instead of CACHE_DRIVER.
    |
    */

    'cache_driver' => env('DOCUWARE_CACHE_DRIVER', env('CACHE_STORE', 'file')),

    /*
    |--------------------------------------------------------------------------
    | Requests timeout
    |--------------------------------------------------------------------------
    | This variable is optional and only used if you want to set the request timeout manually.
    |
    */

    'timeout' => env('DOCUWARE_TIMEOUT', 15),

    /*
    |--------------------------------------------------------------------------
    | DocuWare Credentials
    |--------------------------------------------------------------------------
    |
    | Before you can communicate with the DocuWare REST-API it is necessary
    | to enter your credentials. You should specify a url containing the
    | scheme and hostname. In addition add your username and password.
    |
    */

    'credentials' => [
        'url' => env('DOCUWARE_URL'),
        'username' => env('DOCUWARE_USERNAME'),
        'password' => env('DOCUWARE_PASSWORD'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Passphrase
    |--------------------------------------------------------------------------
    |
    | In order to create encrypted URLs we need a passphrase. This enables a
    | secure exchange of DocuWare URLs without anyone being able to modify
    | your query strings. You can find it in the organization settings.
    |
    */

    'passphrase' => env('DOCUWARE_PASSPHRASE'),

    /*
    |--------------------------------------------------------------------------
    | Configurations
    |--------------------------------------------------------------------------
    |
    */
    'configurations' => [
        'search' => [
            'operation' => 'And',

            /*
             * Force Refresh
             * Determine if result list is retrieved from the cache when ForceRefresh is set
             * to false (default) or always a new one is executed when ForceRefresh is set to true.
             */

            'force_refresh' => true,
            'include_suggestions' => false,
            'additional_result_fields' => [],
        ],
        'cache' => [
            'driver' => env('DOCUWARE_CACHE_DRIVER', env('CACHE_STORE', 'file')),
            'lifetime_in_seconds' => env('DOCUWARE_CACHE_LIFETIME_IN_SECONDS', 60),
        ],
        'request' => [
            'timeout_in_seconds' => env('DOCUWARE_TIMEOUT', 60),
        ],

        'client_id' => env('DOCUWARE_CLIENT_ID', 'docuware.platform.net.client'),
        'scope' => env('DOCUWARE_SCOPE', 'docuware.platform'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tests
    |--------------------------------------------------------------------------
    |
    */
    'tests' => [
        'file_cabinet_id' => env('DOCUWARE_TESTS_FILE_CABINET_ID'),
        'dialog_id' => env('DOCUWARE_TESTS_DIALOG_ID'),
        'basket_id' => env('DOCUWARE_TESTS_BASKET_ID'),
    ],
];

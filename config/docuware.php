<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cache driver
    |--------------------------------------------------------------------------
    | You may like to define a different cache driver than the default Laravel cache driver.
    |
    */

    'cache_driver' => env('DOCUWARE_CACHE_DRIVER', env('CACHE_DRIVER', 'file')),

    /*
   |--------------------------------------------------------------------------
   | Cookies
   |--------------------------------------------------------------------------
   | This variable is optional and only used if you want to set the request cookie manually.
   |
   */

    'cookies' => env('DOCUWARE_COOKIES'),

    /*
   |--------------------------------------------------------------------------
   | Requests timeout
   |--------------------------------------------------------------------------
   | This variable is optional and only used if you want to set the request timeout manually.
   |
   */

    'timeout' => env('DOCUWARE_TIMEOUT', 30),

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
    | Authentication Cookie Lifetime
    |--------------------------------------------------------------------------
    |
    | Here you may define the amount of minutes the authentication cookie is
    | valid. Afterwards it will be removed from the cache and you need to
    | provide a fresh one. By default, the lifetime lasts for one year.
    |
    */

    'cookie_lifetime' => (int) env('DOCUWARE_COOKIE_LIFETIME', 525600),

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
        'document_id' => (int) env('DOCUWARE_TESTS_DOCUMENT_ID'),
        'document_file_size_preview' => (int) env('DOCUWARE_TESTS_DOCUMENT_FILE_SIZE_PREVIEW'),
        'document_file_size' => (int) env('DOCUWARE_TESTS_DOCUMENT_FILE_SIZE'),
        'document_ids' => json_decode(env('DOCUWARE_TESTS_DOCUMENTS_IDS', '[]')),
        'documents_file_size' => (int) env('DOCUWARE_TESTS_DOCUMENTS_FILE_SIZE'),
        'field_name' => env('DOCUWARE_TESTS_FIELD_NAME'),
    ],
];

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | DocuWare Credentials
    |--------------------------------------------------------------------------
    |
    | This configuration option defines your credentials
    | to authenticate with the DocuWare REST-API.
    |
    */

    'credentials' => [
        'url' => env('DOCUWARE_URL'),
        'user' => env('DOCUWARE_USER'),
        'password' => env('DOCUWARE_PASSWORD'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Cookie Lifetime
    |--------------------------------------------------------------------------
    |
    | Here you may define the amount of minutes after the cookie lifetime
    | times out and it is required to refresh a new one. By default,
    | the lifetime lasts for 1 month (43 800 minutes).
    |
    */

    'cookie_lifetime' => (int) env('DOCUWARE_COOKIE_LIFETIME', 43800),

];

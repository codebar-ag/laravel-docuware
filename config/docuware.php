<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cache driver
    |--------------------------------------------------------------------------
    | You may like to define a different cache driver than the default laravel cache driver.
    |
    */

    'cache_driver' => env('DOCUWARE_CACHE_DRIVER', env('CACHE_DRIVER', 'file')),

    /*
   |--------------------------------------------------------------------------
   | Cookies
   |--------------------------------------------------------------------------
   | You can define the DocuWare Request Cookies statically.
   | The DocuWare Request Cookie is usually valid for one year.
   |
   */

    'cookies' => env('DOCUWARE_COOKIES'),

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

];

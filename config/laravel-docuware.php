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
    | Platform path
    |--------------------------------------------------------------------------
    |
    | Postman variable {{Platform}} is usually "DocuWare/Platform". Base URL is
    | rtrim(DOCUWARE_URL,'/').'/'.platform_path
    |
    */

    'platform_path' => env('DOCUWARE_PLATFORM_PATH', 'DocuWare/Platform'),

    /*
    |--------------------------------------------------------------------------
    | Default instance
    |--------------------------------------------------------------------------
    |
    | The name of the instance used when no instance is given explicitly, e.g.
    | DocuWare::documents($cabinet) proxies to DocuWare::instance(default).
    | Single-app users never need to think about instances.
    |
    */

    'default' => env('DOCUWARE_INSTANCE', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Instances (multi-tenancy, first-class)
    |--------------------------------------------------------------------------
    |
    | Each instance is a fully isolated DocuWare connection: its own auth grant,
    | base url, cache namespace, token store entry, and rate limiter. Add one
    | entry per tenant/environment and select it with DocuWare::instance('name').
    |
    | Supported grants: 'credentials' (username/password), 'trusted_user'
    | (impersonation), 'token' (dwtoken login-token exchange).
    |
    */

    'instances' => [
        'default' => [
            'grant' => env('DOCUWARE_GRANT', 'credentials'),
            'url' => env('DOCUWARE_URL'),
            'username' => env('DOCUWARE_USERNAME'),
            'password' => env('DOCUWARE_PASSWORD'),
            'impersonate' => env('DOCUWARE_IMPERSONATE'),
            'token' => env('DOCUWARE_TOKEN'),
            'passphrase' => env('DOCUWARE_PASSPHRASE'),
            'client_id' => env('DOCUWARE_CLIENT_ID', 'docuware.platform.net.client'),
            'scope' => env('DOCUWARE_SCOPE', 'docuware.platform'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | DocuWare Credentials (legacy — maps onto the "default" instance)
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
    | Debug
    |--------------------------------------------------------------------------
    |
    | Secrets are never logged, evented or thrown. When capture_bodies is true
    | request/response bodies are attached to events for debugging — still
    | structurally redacted (Authorization, passwords, tokens, passphrase).
    |
    */

    'debug' => [
        'capture_bodies' => env('DOCUWARE_DEBUG_CAPTURE_BODIES', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Retry (exponential backoff + jitter)
    |--------------------------------------------------------------------------
    |
    | Transient failures (429 / 5xx / timeouts) are retried with backoff,
    | honoring the Retry-After header. Reads are always retried; writes only
    | when safe (idempotent).
    |
    */

    'retry' => [
        'enabled' => env('DOCUWARE_RETRY_ENABLED', true),
        'times' => env('DOCUWARE_RETRY_TIMES', 3),
        'base_interval_ms' => env('DOCUWARE_RETRY_BASE_INTERVAL_MS', 250),
        'max_interval_ms' => env('DOCUWARE_RETRY_MAX_INTERVAL_MS', 10000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate limiting (per instance)
    |--------------------------------------------------------------------------
    |
    | Optional client-side limiter, isolated per instance (per-tenant). Leave
    | disabled unless your DocuWare instance enforces a known request budget.
    |
    */

    'rate_limit' => [
        'enabled' => env('DOCUWARE_RATE_LIMIT_ENABLED', false),
        'allow' => env('DOCUWARE_RATE_LIMIT_ALLOW', 60),
        'per_seconds' => env('DOCUWARE_RATE_LIMIT_PER_SECONDS', 60),
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
        'org_id' => env('DOCUWARE_TESTS_ORG_ID', env('DOCUWARE_TESTS_ORGANIZATION_ID')),
        'group_id' => env('DOCUWARE_TESTS_GROUP_ID'),
        'role_id' => env('DOCUWARE_TESTS_ROLE_ID'),
        'search_dialog_id' => env('DOCUWARE_TESTS_SEARCH_DIALOG_ID'),
        'store_dialog_id' => env('DOCUWARE_TESTS_STORE_DIALOG_ID'),
        'document_id' => env('DOCUWARE_TESTS_DOCUMENT_ID'),

        /*
        | Filtered select list integration test: field and condition must exist on the cabinet dialog.
        */
        'filtered_select_list_field' => env('DOCUWARE_TESTS_FILTERED_SELECT_LIST_FIELD', 'DOCUMENT_TYPE'),
        'filtered_select_list_condition_field' => env('DOCUWARE_TESTS_FILTERED_SELECT_LIST_CONDITION_FIELD', 'DOCUMENT_TYPE'),
        'filtered_select_list_condition_value' => env('DOCUWARE_TESTS_FILTERED_SELECT_LIST_CONDITION_VALUE', '"DocuWare"'),

        'version_management_enabled' => env('DOCUWARE_TESTS_VERSION_MANAGEMENT_ENABLED', false),
        'stamp_id' => env('DOCUWARE_TESTS_STAMP_ID'),
    ],
];

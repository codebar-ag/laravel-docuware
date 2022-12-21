<?php

use CodebarAg\DocuWare\DocuWare;
use CodebarAg\DocuWare\Support\Auth;
use CodebarAg\DocuWare\Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

uses(TestCase::class)->in(__DIR__);

beforeAll(function () {
    if (! config('docuware.cookies')) {
        $cookiePath = storage_path('app/.dwplatformauth');

        if (File::exists($cookiePath)) {
            $cookie = Str::of(File::get($cookiePath))
                ->trim()
                ->trim(PHP_EOL)
                ->trim();

            Cache::put(
                Auth::CACHE_KEY,
                [Auth::COOKIE_NAME => (string) $cookie],
                now()->addDay(),
            );

            return;
        }

        (new DocuWare())->login();

        File::put($cookiePath, Auth::cookies()[Auth::COOKIE_NAME]);
    }
});

afterAll(function () {
    if (File::exists(app_path('app/.dwplatformauth'))) {
        File::delete(app_path('app/.dwplatformauth'));
        (new DocuWare())->logout();
    }
});

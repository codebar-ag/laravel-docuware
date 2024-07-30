<?php

namespace CodebarAg\DocuWare\Tests;

use CodebarAg\DocuWare\DocuWareServiceProvider;
use CodebarAg\DocuWare\Events\DocuWareOAuthLog;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'codebar\\DocuWare\\Database\\Factories\\'.class_basename($modelName).'Factory',
        );

        //        Event::listen(DocuWareResponseLog::class, function (DocuWareResponseLog $event) {
        //            Log::info('Docuware response', [
        //                $event->response->getPendingRequest()->getUrl(),
        //            ]);
        //        });
        //
        //        Event::listen(DocuWareOAuthLog::class, function (DocuWareOAuthLog $event) {
        //            Log::info($event->message, [
        //                'url' => $event->url,
        //                'username' => $event->username,
        //            ]);
        //        });
    }

    protected function getPackageProviders($app): array
    {
        return [
            DocuWareServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}

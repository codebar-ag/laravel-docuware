<?php

use CodebarAg\DocuWare\Connectors\DocuWareConnector;
use CodebarAg\DocuWare\DTO\Config\ConfigWithCredentials;
use CodebarAg\DocuWare\Events\DocuWareOAuthLog;
use CodebarAg\DocuWare\Requests\General\Organization\GetOrganization;

it('can authenticate with DocuWare Credentials', function () {
    Event::fake();

    $connector = new DocuWareConnector(new ConfigWithCredentials(
        username: config('laravel-docuware.credentials.username'),
        password: config('laravel-docuware.credentials.password'),
    ));

    $connector->send(new GetOrganization);

    Event::assertDispatched(DocuWareOAuthLog::class);

})->group('authentication');

it('throws an error if credentials are wrong', function () {
    Event::fake();

    $connector = new DocuWareConnector(new ConfigWithCredentials(
        username: 'wrong-username',
        password: 'wrong-password',
    ));

    $connector->send(new GetOrganization);

    Event::assertDispatched(DocuWareOAuthLog::class);

})
    ->group('authentication')
    ->throws('Invalid user credentials. Please check your username and password.');

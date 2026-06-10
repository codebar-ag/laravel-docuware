<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\General\Organization\GetLoginToken;
use Illuminate\Support\Facades\Event;

it('requests an organization login token', function () {
    Event::fake();

    $token = $this->connector->send(new GetLoginToken)->dto();

    expect($token)->toBeString()->not->toBeEmpty();

    Event::assertDispatched(DocuWareResponseLog::class);
});

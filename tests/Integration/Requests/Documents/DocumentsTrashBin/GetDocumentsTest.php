<?php

use CodebarAg\DocuWare\Data\Documents\TrashPageData;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;

it('can search documents in trash', function () {
    Event::fake();

    $page = DocuWare::trash()->search(page: 1, perPage: 5, searchTerm: 'test');

    expect($page)->toBeInstanceOf(TrashPageData::class);

    Event::assertDispatched(ResponseReceived::class);
})->group('search', 'trash');

it('can search documents in trash with null/default values', function () {
    Event::fake();

    $page = DocuWare::trash()->search();

    expect($page)->toBeInstanceOf(TrashPageData::class);

    Event::assertDispatched(ResponseReceived::class);
})->group('search', 'trash');

// The old trash search exercised the date-filter validation of the legacy search-request builder
// (rejecting >2 dates / diverged ranges via UnableToSearch). The 2.0 trash API
// (DocuWare::trash()->search()) only accepts page/perPage/searchTerm and has no date filtering,
// so these validation cases no longer have an equivalent endpoint.
it('validates trash date-range filters', function () {
    //
})->skip('Trash search no longer supports date-range filters in the 2.0 API.')->group('search', 'trash');

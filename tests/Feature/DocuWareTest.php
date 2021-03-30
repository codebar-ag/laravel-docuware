<?php

namespace codebar\DocuWare\Tests\Feature;

use Cache;
use codebar\DocuWare\DocuWare;
use codebar\DocuWare\Tests\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DocuWareTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $cookiePath = storage_path('app/.dwplatformauth');

        if (File::exists($cookiePath)) {
            $cookie = Str::of(File::get($cookiePath))
                ->trim()
                ->trim(PHP_EOL)
                ->trim();

            Cache::put(
                'docuware.cookies',
                [DocuWare::COOKIE_NAME => (string) $cookie],
                now()->addDay(),
            );

            return;
        }

        $cookie = (new DocuWare())->login();

        File::put($cookiePath, $cookie);
    }

    protected function tearDown(): void
    {
        if (File::exists(app_path('app/.dwplatformauth'))) {
            File::delete(app_path('app/.dwplatformauth'));

            (new DocuWare())->logout();
        }

        parent::tearDown();
    }

    /** @test */
    public function it_does_list_file_cabinets()
    {
        $fileCabinets = (new DocuWare())->getFileCabinets();

        $this->assertInstanceOf(Collection::class, $fileCabinets);
        $this->assertNotCount(0, $fileCabinets);
    }

    /** @test */
    public function it_does_list_fields_for_a_file_cabinet()
    {
        $fileCabinetId = 'f95f2093-e790-495b-af04-7d198a296c5e';

        $fields = (new DocuWare())->getFields($fileCabinetId);

        $this->assertInstanceOf(Collection::class, $fields);
        $this->assertNotCount(0, $fields);
    }

    /** @test */
    public function it_does_list_dialogs_for_a_file_cabinet()
    {
        $fileCabinetId = 'f95f2093-e790-495b-af04-7d198a296c5e';

        $dialogs = (new DocuWare())->getDialogs($fileCabinetId);

        $this->assertInstanceOf(Collection::class, $dialogs);
        $this->assertNotCount(0, $dialogs);
    }

    /** @test */
    public function it_does_list_values_of_a_select_list()
    {
        $fileCabinetId = 'f95f2093-e790-495b-af04-7d198a296c5e';
        $dialogId = '6a84f3da-7514-4116-86df-42b56acd19a7';
        $fieldName = 'DOKUMENTENTYP';

        $types = (new DocuWare())->getSelectList(
            $fileCabinetId,
            $dialogId,
            $fieldName,
        );

        $this->assertSame(['Auftrag', 'Offerte', 'Rechnung'], $types);
    }
}

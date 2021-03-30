<?php

namespace codebar\DocuWare\Tests\Feature;

use codebar\DocuWare\Tests\TestCase;

class DocuWareTest extends TestCase
{
    /** @test */
    public function test_config_values()
    {
        $this->assertSame('https://codebar.docuware.cloud', config('docuware.url'));
    }
}

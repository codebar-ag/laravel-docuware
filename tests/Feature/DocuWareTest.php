<?php

namespace codebar\DocuWare\Tests\Feature;

use codebar\DocuWare\Tests\TestCase;

class DocuWareTest extends TestCase
{
    /** @test */
    public function test_config_values()
    {
        $this->assertSame(config('docuware.foo'), 'bar');                 // ✅ Working
        $this->assertSame(config('docuware.url'), 'https://domain.test'); // ❌ Not working
    }
}

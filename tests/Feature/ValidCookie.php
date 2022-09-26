<?php

namespace CodebarAg\DocuWare\Tests\Feature;

use CodebarAg\DocuWare\DocuWare;
use CodebarAg\DocuWare\Support\Auth;
use CodebarAg\DocuWare\Tests\TestCase;

class ValidCookie extends TestCase
{
    /** @test */
    public function it_does_automatically_login_user()
    {
        $this->assertNull(Auth::cookies());

        (new DocuWare())->getFileCabinets();

        $this->assertArrayHasKey(Auth::COOKIE_NAME, Auth::cookies());
        (new DocuWare())->logout();
        $this->assertNull(Auth::cookies());
    }
}

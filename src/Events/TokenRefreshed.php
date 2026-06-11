<?php

namespace CodebarAg\DocuWare\Events;

use Illuminate\Foundation\Events\Dispatchable;

/**
 * Fired when a fresh OAuth access token is fetched for an instance (cache miss / expiry).
 * Carries no secret — only the instance name and base url.
 */
final class TokenRefreshed
{
    use Dispatchable;

    public function __construct(
        public string $instance,
        public string $url,
    ) {}
}

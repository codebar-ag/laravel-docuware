<?php

namespace CodebarAg\DocuWare\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Client\Response;
use Illuminate\Queue\SerializesModels;

class DocuWareResponseLog
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Response $response)
    {
    }
}

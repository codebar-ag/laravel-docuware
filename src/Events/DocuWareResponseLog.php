<?php

namespace CodebarAg\DocuWare\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Client\Response;
use Illuminate\Queue\SerializesModels;
use Saloon\Contracts\Response as SaloonContracts;
use Saloon\Http\Response as SaloonResponse;

class DocuWareResponseLog
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public Response|SaloonResponse|SaloonContracts $response
    ) {
    }
}

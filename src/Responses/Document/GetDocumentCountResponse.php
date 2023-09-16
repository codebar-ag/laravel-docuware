<?php

namespace CodebarAg\DocuWare\Responses\Document;

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Exceptions\UnableToGetDocumentCount;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Illuminate\Support\Arr;
use Saloon\Contracts\Response;

final class GetDocumentCountResponse
{
    public static function fromResponse(Response $response): int
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $content = $response->throw()->json();
        throw_unless(Arr::has($content, 'Group'), UnableToGetDocumentCount::noCount());

        $group = Arr::get($content, 'Group');
        throw_unless(Arr::has($group, '0'), UnableToGetDocumentCount::noGroupKeyIndexZero());
        $group = Arr::get($group, '0');

        throw_unless(Arr::has($group, 'Count'), UnableToGetDocumentCount::noCount());

        return Arr::get($group, 'Count');
    }
}

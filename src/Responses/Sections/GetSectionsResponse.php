<?php

namespace CodebarAg\DocuWare\Responses\Sections;

use CodebarAg\DocuWare\DTO\Section;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Saloon\Http\Response;

final class GetSectionsResponse
{
    public static function fromResponse(Response $response): Collection|Enumerable
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $res = $response->throw()->json();

        $sections = collect();

        foreach ($res['Section'] as $section) {
            $sections->push(Section::fromJson($section));
        }

        return $sections;
    }
}

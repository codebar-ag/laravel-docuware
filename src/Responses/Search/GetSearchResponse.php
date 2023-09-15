<?php

namespace CodebarAg\DocuWare\Responses\Search;

use CodebarAg\DocuWare\DTO\DocumentPaginator;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Exception;
use Saloon\Contracts\Response;

final class GetSearchResponse
{
    public static function fromResponse(Response $response, $page, $perPage): DocumentPaginator
    {
        event(new DocuWareResponseLog($response));

        try {
            EnsureValidResponse::from($response);

            $data = $response->throw()->json();
        } catch (Exception $e) {
            return DocumentPaginator::fromFailed($e);
        }

        return DocumentPaginator::fromJson(
            $data,
            $page,
            $perPage,
        );
    }
}

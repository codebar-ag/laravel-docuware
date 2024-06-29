<?php

namespace CodebarAg\DocuWare\Responses\Search;

use CodebarAg\DocuWare\DTO\Documents\TrashDocumentPaginator;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Exception;
use Saloon\Http\Response;

final class GetTrashSearchResponse
{
    public static function fromResponse(Response $response, $page, $perPage): TrashDocumentPaginator
    {
        event(new DocuWareResponseLog($response));

        try {
            EnsureValidResponse::from($response);

            $data = $response->throw()->json();
        } catch (Exception $e) {
            return TrashDocumentPaginator::fromFailed($e);
        }

        return TrashDocumentPaginator::fromJson(
            $data,
            $page,
            $perPage,
        );
    }
}

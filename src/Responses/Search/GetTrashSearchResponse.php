<?php

namespace CodebarAg\DocuWare\Responses\Search;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use CodebarAg\DocuWare\DTO\Documents\TrashDocumentPaginator;
use Exception;
use Saloon\Http\Response;

final class GetTrashSearchResponse
{
    use HandlesDocuWareResponse;

    public static function fromResponse(Response $response, int $page, int $perPage): TrashDocumentPaginator
    {
        try {
            $data = self::validated($response)->json();
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

<?php

namespace CodebarAg\DocuWare\Responses\Search;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use CodebarAg\DocuWare\DTO\Documents\DocumentPaginator;
use Exception;
use Saloon\Http\Response;

final class GetSearchResponse
{
    use HandlesDocuWareResponse;

    public static function fromResponse(Response $response, int $page, int $perPage): DocumentPaginator
    {
        try {
            $data = self::validated($response)->json();
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

<?php

namespace CodebarAg\DocuWare\Responses\FileCabinets\Search;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use CodebarAg\DocuWare\DTO\Documents\DocumentPaginator;
use Exception;
use Saloon\Http\Response;

final class GetDocumentsFromAFileCabinetResponse
{
    use HandlesDocuWareResponse;

    public static function fromResponse(Response $response, int $page = 1, int $perPage = 50): DocumentPaginator
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

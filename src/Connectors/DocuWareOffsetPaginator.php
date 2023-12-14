<?php

namespace CodebarAg\DocuWare\Connectors;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\OffsetPaginator as SaloonOffsetPaginator;
use Saloon\PaginationPlugin\Paginator;

class DocuWareOffsetPaginator extends SaloonOffsetPaginator
{
    protected ?int $perPageLimit = 10000;

    public  function getSinglePage(int $page): Paginator
    {
        return $this->setStartPage($page)->setMaxPages($page);
    }

    protected function isLastPage(Response $response): bool
    {
        $count = Arr::get($response->json(), 'Count');
        return $this->getOffset() >= (int) Arr::get($count, 'Value');
    }

    protected function getTotalPages(Response $response): int
    {
        $count = Arr::get($response->json(), 'Count');
        return ceil((Arr::get($count, 'Value') / $this->perPageLimit));
    }

    protected function getPageItems(Response $response, Request $request): array
    {
        return [
            $response->dto(),
        ];
    }

    protected function applyPagination(Request $request): Request
    {
        $request->query()->merge([
            'count' => $this->perPageLimit,
            'start' => $this->getOffset(),
        ]);

        return $request;
    }
}

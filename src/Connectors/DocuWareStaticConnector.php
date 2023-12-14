<?php

namespace CodebarAg\DocuWare\Connectors;

use CodebarAg\DocuWare\Support\Auth;
use CodebarAg\DocuWare\Support\EnsureValidCookie;
use CodebarAg\DocuWare\Support\EnsureValidCredentials;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Arr;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\HasPagination;
use Saloon\PaginationPlugin\OffsetPaginator;

class DocuWareStaticConnector extends Connector implements HasPagination
{
    public CookieJar $cookieJar;

    public function __construct()
    {
        EnsureValidCredentials::check();
        EnsureValidCookie::check();

        $this->cookieJar = Auth::cookieJar() ?? throw new \Exception('No cookie jar found');
    }

    /**
     * @throws \Exception
     */
    public function resolveBaseUrl(): string
    {
        return config('laravel-docuware.credentials.url').'/DocuWare/Platform';
    }

    public function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    public function defaultConfig(): array
    {
        return [
            'timeout' => config('laravel-docuware.timeout'),
            'cookies' => $this->cookieJar,
        ];
    }

    public function getCoookieJar(): CookieJar
    {
        return $this->cookieJar;
    }

    public function paginate(Request $request): OffsetPaginator
    {
        //        return new DocuWareOffsetPaginator(connector: $this, request: $request);

        return new class(connector: $this, request: $request) extends OffsetPaginator
        {
            protected ?int $perPageLimit = 10000;

            protected function isLastPage(Response $response): bool
            {
                $count = Arr::get($response->json(), 'Count');

                return $this->getOffset() >= (int) Arr::get($count, 'Value');
            }

            protected function getTotalPages(Response $response): int
            {
                $count = Arr::get($response->json(), 'Count');

                return (int) ceil((Arr::get($count, 'Value') / $this->perPageLimit));
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
        };
    }
}

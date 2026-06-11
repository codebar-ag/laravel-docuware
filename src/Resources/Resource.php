<?php

namespace CodebarAg\DocuWare\Resources;

use CodebarAg\DocuWare\Client\DocuWareClient;
use CodebarAg\DocuWare\Support\JsonArrays;
use CodebarAg\DocuWare\Transport\ResponseValidator;
use Illuminate\Support\Collection;
use Saloon\Http\Request;
use Saloon\Http\Response;

/**
 * Base for the resource gateways — the public, domain-shaped API. Each resource sends thin
 * Saloon requests through its instance connector and deserializes via the `*Data` classes.
 * Saloon is never exposed to consumers.
 */
abstract class Resource
{
    public function __construct(
        protected readonly DocuWareClient $client,
    ) {}

    /**
     * Send a request through the instance connector, throwing a typed exception on any non-2xx.
     */
    protected function send(Request $request): Response
    {
        $response = $this->client->connector()->send($request);

        ResponseValidator::validate($response, $this->client->name());

        return $response;
    }

    /**
     * Map a list of records at the given JSON key (e.g. `User`, `Dialog`, `Item`) to Data
     * objects. `$key = null` maps the whole body.
     *
     * @template TValue
     *
     * @param  callable(array<string, mixed>): TValue  $map
     * @return Collection<int, TValue>
     */
    protected function mapList(Response $response, ?string $key, callable $map): Collection
    {
        $raw = $key === null ? $response->json() : $response->json($key);

        return collect(JsonArrays::listOfRecords($raw))
            ->map($map)
            ->values();
    }
}

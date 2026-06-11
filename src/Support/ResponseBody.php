<?php

namespace CodebarAg\DocuWare\Support;

use Saloon\Http\Response;

final class ResponseBody
{
    /**
     * Decode a response body to an array, tolerating either JSON or XML.
     *
     * The DocuWare Platform API negotiates content per the `Accept` header, but some
     * instances ignore it and return XML (e.g. `Home/IdentityServiceInfo`). Decoding
     * defensively keeps the OAuth discovery flow working regardless of the instance.
     *
     * @return array<string, mixed>
     */
    public static function toArray(Response $response): array
    {
        $body = trim((string) $response->body());

        if ($body === '') {
            return [];
        }

        $json = json_decode($body, true);

        if (is_array($json)) {
            return $json;
        }

        $xml = @simplexml_load_string($body);

        if ($xml !== false) {
            $decoded = json_decode((string) json_encode($xml), true);

            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }
}

<?php

namespace CodebarAg\DocuWare\Security;

/**
 * Structural redaction of secrets before anything is logged, evented, or thrown.
 *
 * Redaction is by key (case-insensitive) for arrays/headers, and by pattern for free-form
 * strings (Bearer tokens, password/token query or JSON fragments). Secrets never leave the
 * process in clear text — this is the single choke point the leak test asserts against.
 */
final class Redactor
{
    public const PLACEHOLDER = '[REDACTED]';

    /**
     * Keys whose values are always replaced wholesale, regardless of nesting.
     *
     * @var list<string>
     */
    private const SECRET_KEYS = [
        'authorization',
        'password',
        'pwd',
        'token',
        'access_token',
        'refresh_token',
        'id_token',
        'passphrase',
        'client_secret',
        'cookie',
        'set-cookie',
    ];

    /**
     * Redact a structure (headers, body arrays, exception context). Scalars pass through
     * {@see self::string()}; arrays are walked recursively with key-based redaction.
     */
    public function redact(mixed $value): mixed
    {
        if (is_array($value)) {
            $out = [];
            foreach ($value as $key => $item) {
                $out[$key] = $this->isSecretKey($key)
                    ? self::PLACEHOLDER
                    : $this->redact($item);
            }

            return $out;
        }

        if (is_string($value)) {
            return $this->string($value);
        }

        return $value;
    }

    /**
     * Redact secret patterns inside a free-form string (e.g. a serialized request body or URL).
     */
    public function string(string $value): string
    {
        // Bearer / token-type prefixes.
        $value = (string) preg_replace('/\bBearer\s+[A-Za-z0-9\-._~+\/]+=*/i', 'Bearer '.self::PLACEHOLDER, $value);

        // key=value (query / form) and "key":"value" (JSON) for known secret keys.
        $keys = implode('|', array_map('preg_quote', self::SECRET_KEYS));
        $value = (string) preg_replace('/("(?:'.$keys.')"\s*:\s*")[^"]*(")/i', '$1'.self::PLACEHOLDER.'$2', $value);
        $value = (string) preg_replace('/\b('.$keys.')=([^&\s]+)/i', '$1='.self::PLACEHOLDER, $value);

        return $value;
    }

    private function isSecretKey(int|string $key): bool
    {
        return is_string($key) && in_array(strtolower($key), self::SECRET_KEYS, true);
    }
}

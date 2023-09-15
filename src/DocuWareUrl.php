<?php

namespace CodebarAg\DocuWare;

use Carbon\Carbon;
use CodebarAg\DocuWare\Exceptions\UnableToMakeUrl;
use CodebarAg\DocuWare\Support\EnsureValidCredentials;
use CodebarAg\DocuWare\Support\EnsureValidPassphrase;
use CodebarAg\DocuWare\Support\URL;

class DocuWareUrl
{
    protected ?string $fileCabinetId = null;

    protected ?string $basketId = null;

    protected ?int $documentId = null;

    protected ?Carbon $validUntil = null;

    public function fileCabinet(string $fileCabinetId): self
    {
        $this->fileCabinetId = $fileCabinetId;

        return $this;
    }

    public function basket(string $basketId): self
    {
        $this->basketId = $basketId;

        return $this;
    }

    public function document(int $documentId): self
    {
        $this->documentId = $documentId;

        return $this;
    }

    public function validUntil(Carbon $date): self
    {
        $this->validUntil = $date;

        return $this;
    }

    public function make(): string
    {
        $this->guard();

        $credentials = sprintf(
            'User=%s\nPwd=%s',
            config('docuware.credentials.username'),
            config('docuware.credentials.password'),
        );

        $lc = URL::formatWithBase64($credentials);

        if ($this->fileCabinetId) {
            $source = "fc={$this->fileCabinetId}";
        } else {
            $source = "scid={$this->basketId}";
        }

        $data = implode('&', [
            "lc={$lc}",
            'p=V',
            "did={$this->documentId}",
            $source,
        ]);

        if ($this->validUntil) {
            $data = implode('&', [
                $data,
                'vu='.$this->validUntil->format('Y-m-d\TH:i:s\Z'),
            ]);
        }

        // Source: https://support.docuware.com/en-US/forums/help-with-technical-problems/ea9618df-c491-e911-80e7-0003ff59a7c6
        $key = utf8_encode(config('docuware.passphrase'));
        $passphrase = hash('sha512', $key, true);
        $encryption_key = substr($passphrase, 0, 32);
        $iv = substr($passphrase, 32, 16);

        $encrypted = openssl_encrypt(
            data: $data,
            cipher_algo: 'aes-256-cbc',
            passphrase: $encryption_key,
            iv: $iv,
        );

        return sprintf(
            '%s/DocuWare/Platform/WebClient/Integration?ep=%s',
            config('docuware.credentials.url'),
            URL::format($encrypted),
        );
    }

    protected function guard(): void
    {
        EnsureValidCredentials::check();

        EnsureValidPassphrase::check();

        throw_if(
            is_null($this->documentId),
            UnableToMakeUrl::documentNotSet(),
        );

        throw_if(
            is_null($this->fileCabinetId) && is_null($this->basketId),
            UnableToMakeUrl::sourceNotSet(),
        );
    }
}

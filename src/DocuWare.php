<?php

namespace CodebarAg\DocuWare;

class DocuWare
{
    public function searchRequestBuilder(): DocuWareSearchRequestBuilder
    {
        return new DocuWareSearchRequestBuilder;
    }

    public function url(
        string $url,
        string $username,
        string $password,
        string $passphrase
    ): DocuWareUrl {
        return new DocuWareUrl(
            url: $url,
            username: $username,
            password: $password,
            passphrase: $passphrase,
        );
    }
}

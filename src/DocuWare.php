<?php

namespace codebar\DocuWare;

use codebar\DocuWare\DTO\Dialog;
use codebar\DocuWare\DTO\Field;
use codebar\DocuWare\DTO\FileCabinet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DocuWare
{
    const COOKIE_NAME = '.DWPLATFORMAUTH';

    protected string $domain;

    public function __construct()
    {
        $this->domain = Str::of(config('docuware.url'))
            ->after('//')
            ->beforeLast('/');
    }

    public function login(): string
    {
        if (Cache::has('docuware.cookies')) {
            return Cache::get('docuware.cookies');
        }

        $url = sprintf(
            '%s/docuware/platform/Account/Logon',
            config('docuware.url'),
        );

        $response = Http::asForm()
            ->acceptJson()
            ->post($url, [
                'UserName' => config('docuware.user'),
                'Password' => config('docuware.password'),
            ])
            ->throw();

        $cookie = collect($response->cookies()->toArray())
            ->reject(fn (array $cookie) => $cookie['Value'] === '')
            ->firstWhere('Name', self::COOKIE_NAME);

        Cache::put(
            'docuware.cookies',
            [self::COOKIE_NAME => $cookie['Value']],
            now()->addDay(),
        );

        return $cookie['Value'];
    }

    public function logout(): void
    {
        $cookie = Cache::pull('docuware.cookies');

        $url = sprintf(
            '%s/docuware/platform/Account/Logoff',
            config('docuware.url'),
        );

        Http::withCookies($cookie, $this->domain)->get($url);
    }

    /** @return Collection|FileCabinet[] */
    public function getFileCabinets(): Collection
    {
        $url = sprintf(
            '%s/docuware/platform/FileCabinets?orgid=1',
            config('docuware.url'),
        );

        $cabinets = Http::acceptJson()
            ->withCookies(Cache::get('docuware.cookies'), $this->domain)
            ->get($url)
            ->throw()
            ->json('FileCabinet');

        return collect($cabinets)->map(fn (array $cabinet) => FileCabinet::fromJson($cabinet));
    }

    /** @return Collection|Field[] */
    public function getFields(string $fileCabinetId): Collection
    {
        $url = sprintf(
            '%s/docuware/platform/FileCabinets/%s',
            config('docuware.url'),
            $fileCabinetId,
        );

        $fields = Http::acceptJson()
            ->withCookies(Cache::get('docuware.cookies'), $this->domain)
            ->get($url)
            ->throw()
            ->json('Fields');

        return collect($fields)->map(fn (array $field) => Field::fromJson($field));
    }

    /** @return Collection|Dialog[] */
    public function getDialogs(string $fileCabinetId): Collection
    {
        $url = sprintf(
            '%s/docuware/platform/FileCabinets/%s/Dialogs',
            config('docuware.url'),
            $fileCabinetId,
        );

        $dialogs = Http::acceptJson()
            ->withCookies(Cache::get('docuware.cookies'), $this->domain)
            ->get($url)
            ->throw()
            ->json('Dialog');

        return collect($dialogs)->map(fn (array $dialog) => Dialog::fromJson($dialog));
    }
}

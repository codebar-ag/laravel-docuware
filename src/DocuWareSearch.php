<?php

namespace CodebarAg\DocuWare;

use Carbon\Carbon;
use CodebarAg\DocuWare\DTO\DocumentPaginator;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Exceptions\UnableToSearchDocuments;
use CodebarAg\DocuWare\Support\ParseValue;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DocuWareSearch
{
    protected ?string $fileCabinetId = null;
    protected ?string $dialogId = null;
    protected array $additionalFileCabinetIds = [];
    protected int $page = 1;
    protected int $perPage = 50;
    protected ?string $searchTerm = null;
    protected ?Carbon $dateFrom = null;
    protected ?Carbon $dateUntil = null;
    protected string $orderField = 'DWSTOREDATETIME';
    protected string $orderDirection = 'asc';
    protected array $filters = [];

    protected string $domain;

    public function __construct()
    {
        $this->domain = ParseValue::domain();
    }

    public function fileCabinet(string $fileCabinetId): self
    {
        $this->fileCabinetId = $fileCabinetId;

        return $this;
    }

    public function dialog(string $dialogId): self
    {
        $this->dialogId = $dialogId;

        return $this;
    }

    public function additionalFileCabinets(array $additionalCabinets): self
    {
        $this->additionalFileCabinetIds = $additionalCabinets;

        return $this;
    }

    public function page(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function perPage(int $perPage): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function fulltext(string $searchTerm): self
    {
        $this->searchTerm = $searchTerm;

        return $this;
    }

    public function dateFrom(Carbon $dateFrom): self
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    public function dateUntil(Carbon $dateUntil): self
    {
        $this->dateUntil = $dateUntil;

        return $this;
    }

    public function orderBy(string $field, string $direction = 'asc'): self
    {
        $this->orderField = "\"{$field}\"";

        $this->orderDirection = $direction; // 'asc' || 'desc'

        return $this;
    }

    public function filter(string $name, mixed $value): self
    {
        if (is_string($value)) {
            $value = "\"{$value}\"";
        }

        $this->filters[] = [$name, Arr::wrap($value)];

        return $this;
    }

    public function get(): DocumentPaginator
    {
        $this->guard();

        $url = sprintf(
            '%s/docuware/platform/FileCabinets/%s/Query/DialogExpression?dialogId=%s',
            config('docuware.url'),
            $this->fileCabinetId,
            $this->dialogId,
        );

        $condition = [];

        if (Str::length($this->searchTerm) >= 1) {
            $condition[] = [
                'DBName' => 'DocuWareFulltext',
                'Value' => [$this->searchTerm],
            ];
        }

        if ($this->dateFrom || $this->dateUntil) {
            $condition[] = [
                'DBName' => 'DWSTOREDATETIME',
                'Value' => [
                    $this->dateFrom?->startOfDay(),
                    $this->dateUntil?->endOfDay(),
                ],
            ];
        }

        foreach ($this->filters as [$name, $value]) {
            $condition[] = [
                'DBName' => $name,
                'Value' => $value,
            ];
        }

        $response = Http::acceptJson()
            ->withCookies(Cache::get('docuware.cookies'), $this->domain)
            ->post($url, [
                'Count' => $this->perPage,
                'Start' => ($this->page - 1) * $this->perPage,
                'Condition' => $condition,
                'AdditionalCabinets' => $this->additionalFileCabinetIds,
                'SortOrder' => [
                    [
                        'Field' => $this->orderField,
                        'Direction' => $this->orderDirection,
                    ],
                ],
                'Operation' => 'And',
                'ForceRefresh' => true,
                'IncludeSuggestions' => false,
                'AdditionalResultFields' => [],
            ]);

        event(new DocuWareResponseLog($response));

        $data = $response->throw()->json();

        return DocumentPaginator::fromJson(
            $data,
            $this->page,
            $this->perPage,
        );
    }

    protected function guard(): void
    {
        throw_if(
            is_null($this->fileCabinetId),
            UnableToSearchDocuments::cabinetNotSet(),
        );

        throw_if(
            is_null($this->dialogId),
            UnableToSearchDocuments::dialogNotSet(),
        );
    }
}

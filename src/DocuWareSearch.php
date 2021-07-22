<?php

namespace CodebarAg\DocuWare;

use Carbon\Carbon;
use CodebarAg\DocuWare\DTO\DocumentPaginator;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Exceptions\UnableToSearch;
use CodebarAg\DocuWare\Support\Auth;
use CodebarAg\DocuWare\Support\EnsureValidCookie;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Exception;
use Illuminate\Support\Arr;
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

    public function fileCabinet(string $fileCabinetId): self
    {
        $this->fileCabinetId = $fileCabinetId;

        return $this;
    }

    public function fileCabinets(array $fileCabinetIds): self
    {
        $this->fileCabinetId = $fileCabinetIds[0] ?? null;

        $this->additionalFileCabinetIds = array_slice($fileCabinetIds, 1);

        return $this;
    }

    public function dialog(string $dialogId): self
    {
        $this->dialogId = $dialogId;

        return $this;
    }

    public function page(?int $page): self
    {
        if (is_null($page)) {
            $this->page = 1;
        } else {
            $this->page = $page;
        }

        return $this;
    }

    public function perPage(?int $perPage): self
    {
        if (is_null($perPage)) {
            $this->perPage = 50;
        } else {
            $this->perPage = $perPage;
        }

        return $this;
    }

    public function fulltext(?string $searchTerm): self
    {
        $this->searchTerm = $searchTerm;

        return $this;
    }

    public function dateFrom(?Carbon $dateFrom): self
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    public function dateUntil(?Carbon $dateUntil): self
    {
        $this->dateUntil = $dateUntil;

        return $this;
    }

    public function orderBy(string $field, ?string $direction = 'asc'): self
    {
        $this->orderField = $field;

        if (is_null($direction)) {
            $this->orderDirection = 'asc';
        } else {
            $this->orderDirection = $direction; // Supported values: 'asc', 'desc'
        }

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
            '%s/DocuWare/Platform/FileCabinets/%s/Query/DialogExpression',
            config('docuware.credentials.url'),
            $this->fileCabinetId,
        );

        if ($this->dialogId) {
            $url .= "?dialogId={$this->dialogId}";
        }

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
            if (empty($value)) {
                continue;
            }

            $condition[] = [
                'DBName' => $name,
                'Value' => $value,
            ];
        }

        $response = Http::acceptJson()
            ->withCookies(Auth::cookies(), Auth::domain())
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

        try {
            EnsureValidResponse::from($response);

            $data = $response->throw()->json();
        } catch (Exception $e) {
            return DocumentPaginator::fromFailed($e);
        }

        return DocumentPaginator::fromJson(
            $data,
            $this->page,
            $this->perPage,
        );
    }

    protected function guard(): void
    {
        EnsureValidCookie::check();

        throw_if(
            is_null($this->fileCabinetId),
            UnableToSearch::cabinetNotSet(),
        );

        throw_if(
            $this->page <= 0,
            UnableToSearch::invalidPageNumber($this->page),
        );

        throw_if(
            $this->perPage <= 0,
            UnableToSearch::invalidPerPageNumber($this->perPage),
        );
    }
}

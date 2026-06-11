<img src="https://banners.beyondco.de/Laravel%20DocuWare.png?theme=light&packageManager=composer+require&packageName=codebar-ag%2Flaravel-docuware&pattern=circuitBoard&style=style_1&description=An+opinionated+way+to+integrate+DocuWare+with+Laravel&md=1&showWatermark=0&fontSize=175px&images=document-report">

[![Latest Version on Packagist](https://img.shields.io/packagist/v/codebar-ag/laravel-docuware.svg?style=flat-square)](https://packagist.org/packages/codebar-ag/laravel-docuware)
[![Total Downloads](https://img.shields.io/packagist/dt/codebar-ag/laravel-docuware.svg?style=flat-square)](https://packagist.org/packages/codebar-ag/laravel-docuware)
[![GitHub-Tests](https://github.com/codebar-ag/laravel-docuware/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/codebar-ag/laravel-docuware/actions/workflows/run-tests.yml)
[![GitHub Code Style](https://github.com/codebar-ag/laravel-docuware/actions/workflows/fix-php-code-style-issues.yml/badge.svg?branch=main)](https://github.com/codebar-ag/laravel-docuware/actions/workflows/fix-php-code-style-issues.yml)
[![PHPStan](https://github.com/codebar-ag/laravel-docuware/actions/workflows/phpstan.yml/badge.svg)](https://github.com/codebar-ag/laravel-docuware/actions/workflows/phpstan.yml)

An opinionated, modern integration for the [DocuWare Platform REST API](https://developer.docuware.com/rest/index.html).
You work with **resources** and **immutable data objects** — never raw HTTP.

> This package is **not** designed as a replacement for the official DocuWare REST API. It is an
> opinionated convenience layer on top of it.

---

> # ⚠️ v14 is a breaking, ground-up release
>
> **v14 has a completely new syntax and is NOT backward compatible with v13 or any earlier release.**
>
> Every entry point changed: the old `DocuWare`/`Connectors`/`Requests`/`DocuWareSearchRequestBuilder`
> classes and the hand-rolled DTOs are **gone**. v14 is built around a single facade, domain
> **resources**, immutable `*Data` objects, and a fluent search builder.
>
> There is **no in-place upgrade path** and **no compatibility shim** — calling code must be rewritten.
> Pin to `^13.0` if you are not ready. When you are, jump straight to
> [**What changed in v14 (and why)**](#what-changed-in-v14-and-why) for the old → new mapping and the
> reasoning behind every removal.

---

## Table of contents

- [Highlights](#highlights)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Authentication & grants](#authentication--grants)
- [Connecting (config file or runtime DTO)](#connecting-config-file-or-runtime-dto)
- [Quickstart](#quickstart)
- [Resources](#resources)
- [Search builder](#search-builder)
- [Index field values](#index-field-values)
- [Immutable data & withers](#immutable-data--withers)
- [Enums](#enums)
- [Encrypted document URLs](#encrypted-document-urls)
- [Endpoint catalog](#endpoint-catalog)
- [Data object (DTO) reference](#data-object-dto-reference)
- [Caching](#caching)
- [Error handling](#error-handling)
- [Events](#events)
- [Security](#security)
- [What changed in v14 (and why)](#what-changed-in-v14-and-why)
- [Testing](#testing)
- [Credits](#credits)
- [License](#license)

## Highlights

- 🎯 **Small, domain-shaped API** — `DocuWare::documents($cabinet)->search()->cursor()`.
- 🧊 **Immutable data** via [`spatie/laravel-data`](https://spatie.be/docs/laravel-data) with `->copyWith(...)` withers.
- 🏢 **Multi-tenant first-class** — by config name **or** a runtime connection DTO.
- 🔢 **Typed index fields** — `IndexFields::make()->text(...)->number(...)->date(...)->table(...)` converts
  PHP values into the exact DocuWare wire format for you.
- 🔒 **Secure by default** — secrets are never logged, evented, or thrown.
- ♻️ **Resilient** — encrypted + lock-guarded token store, retry with backoff honoring `Retry-After`.
- 🧯 **One error model** — every failure descends from `DocuWareException`.
- 🧪 **Fully fakeable & typed** — PHPStan max, offline fixture replay.

## Requirements

| Version       | PHP         | Laravel       | DocuWare Cloud |
|---------------|-------------|---------------|----------------|
| **v14**       | ^8.4 – ^8.5 | 13.*          | ✅             |
| v13           | ^8.3 – ^8.4 | 13.*          | ✅             |
| v12           | ^8.2 – ^8.4 | 12.*          | ✅             |
| v11 (alpha)   | ^8.2        | 11.*          | ✅             |
| > v4.0        | ^8.2        | 11.*          | ✅             |
| > v3.0        | ^8.2        | 10.*          | ✅             |
| > v2.0        | ^8.1        | 9.*           | ✅             |
| > v1.2        | ^8.1        | 9.*           | ✅             |
| < v1.2        | ^8.0        | 8.*           | ✅             |

> **v14 raises the minimum PHP to 8.4.** Laravel 13 stays the target framework.

## Installation

```bash
composer require codebar-ag/laravel-docuware
php artisan vendor:publish --tag=laravel-docuware-config
```

Configure the default instance in `.env`:

```dotenv
DOCUWARE_URL=https://your-instance.docuware.cloud
DOCUWARE_USERNAME=your-user
DOCUWARE_PASSWORD=your-password
DOCUWARE_PASSPHRASE=your-url-passphrase
```

## Configuration

The default instance reads the flat `DOCUWARE_*` env keys above. Everything else has sane defaults;
publish the config to tune caching, retries, rate limiting, and multi-tenant instances.

<details>
<summary><strong>Full config reference (<code>config/laravel-docuware.php</code>)</strong></summary>

| Key | Env | Default | Purpose |
| --- | --- | --- | --- |
| `default` | `DOCUWARE_INSTANCE` | `default` | Which instance bare facade calls use |
| `platform_path` | `DOCUWARE_PLATFORM_PATH` | `DocuWare/Platform` | REST base path appended to the URL |
| `timeout` | `DOCUWARE_TIMEOUT` | `15` | Default request timeout (seconds) |
| `passphrase` | `DOCUWARE_PASSPHRASE` | — | Passphrase for encrypted document URLs |
| `instances.*` | — | — | Named multi-tenant connections (see below) |
| `credentials.url` | `DOCUWARE_URL` | — | Legacy flat URL for the `default` instance |
| `credentials.username` | `DOCUWARE_USERNAME` | — | Legacy flat username |
| `credentials.password` | `DOCUWARE_PASSWORD` | — | Legacy flat password |
| `configurations.client_id` | `DOCUWARE_CLIENT_ID` | `docuware.platform.net.client` | OAuth client id |
| `configurations.scope` | `DOCUWARE_SCOPE` | `docuware.platform` | OAuth scope |
| `configurations.cache.driver` | `DOCUWARE_CACHE_DRIVER` | `file` | Cache store for responses & tokens |
| `configurations.cache.lifetime_in_seconds` | `DOCUWARE_CACHE_LIFETIME_IN_SECONDS` | `60` | Cache TTL for `Cacheable` requests |
| `configurations.request.timeout_in_seconds` | `DOCUWARE_TIMEOUT` | `60` | Per-request timeout |
| `debug.capture_bodies` | `DOCUWARE_DEBUG_CAPTURE_BODIES` | `false` | Attach (redacted) bodies to `ResponseReceived` |
| `retry.enabled` | `DOCUWARE_RETRY_ENABLED` | `true` | Exponential backoff on transient failures |
| `retry.times` | `DOCUWARE_RETRY_TIMES` | `3` | Max retry attempts |
| `retry.base_interval_ms` | `DOCUWARE_RETRY_BASE_INTERVAL_MS` | `250` | First backoff interval |
| `retry.max_interval_ms` | `DOCUWARE_RETRY_MAX_INTERVAL_MS` | `10000` | Backoff ceiling |
| `rate_limit.enabled` | `DOCUWARE_RATE_LIMIT_ENABLED` | `false` | Per-instance client-side rate limiting |
| `rate_limit.allow` | `DOCUWARE_RATE_LIMIT_ALLOW` | `60` | Requests allowed per window |
| `rate_limit.per_seconds` | `DOCUWARE_RATE_LIMIT_PER_SECONDS` | `60` | Window length (seconds) |

</details>

## Authentication & grants

OAuth is handled for you: the package discovers the identity service, exchanges credentials for an
access token, encrypts it, caches it per instance, and refreshes it under a lock before it expires.
You never call the token endpoint yourself.

Three grants are supported:

| Grant | `grant` value | Needs | Use case |
| --- | --- | --- | --- |
| Credentials | `credentials` | url, username, password | Standard username/password login |
| Trusted user | `trusted_user` | url, username, password, impersonate | Authenticate as a trusted user, act as another |
| DocuWare token | `token` | url, token | Exchange a DocuWare login token (`dwtoken`) |

## Connecting (config file or runtime DTO)

There are two ways to connect — pick whichever fits your tenancy model.

### 1. Named instances in config

Best when tenants are known ahead of time. Add them to `config/laravel-docuware.php`:

```php
'instances' => [
    'acme'   => ['grant' => 'credentials', 'url' => '…', 'username' => '…', 'password' => '…'],
    'globex' => ['grant' => 'token', 'url' => '…', 'token' => '…'],
],
```

```php
DocuWare::instance('acme')->documents($cabinetId)->search()->get();
```

### 2. A runtime connection DTO (database-driven multi-tenancy)

Multi-tenancy is **not** limited to config files. When connections live in your database — the common
case — build a typed config DTO at runtime and hand it to `DocuWare::connection()`:

```php
use CodebarAg\DocuWare\Facades\DocuWare;
use CodebarAg\DocuWare\Config\CredentialsConfig;

$client = DocuWare::connection(CredentialsConfig::for(
    url:      $tenant->docuware_url,
    username: $tenant->docuware_user,
    password: $tenant->docuware_secret,
    name:     $tenant->slug,            // namespaces this tenant's cache + token entry
));

$client->documents($cabinetId)->search()->fullText('invoice')->get();
```

Each grant has a typed `for()` factory that fills sensible defaults (cache driver, lifetime, timeout,
client id, scope) — override any of them as named arguments:

```php
use CodebarAg\DocuWare\Config\{CredentialsConfig, TokenConfig, TrustedUserConfig};

CredentialsConfig::for(url: '…', username: '…', password: '…', name: 'acme');
TokenConfig::for(url: '…', token: '…', name: 'globex');
TrustedUserConfig::for(url: '…', username: '…', password: '…', impersonate: 'bob', name: 'acme');
```

`DocuWare::instance(...)` also accepts a DTO directly, so `DocuWare::instance($config)` and
`DocuWare::connection($config)` are equivalent. Runtime connections are **not** name-cached, so two
tenants may safely reuse a `name`; their tokens stay isolated by URL + credentials.

Every connection is independent: its own connector, cache namespace, encrypted token entry, and
rate limiter.

## Quickstart

```php
use CodebarAg\DocuWare\Facades\DocuWare;
use CodebarAg\DocuWare\Data\Documents\DocumentData;
use CodebarAg\DocuWare\Data\Write\IndexFields;
use CodebarAg\DocuWare\Enums\TargetFileType;

// Lazily iterate EVERY matching document across all pages (memory-safe).
DocuWare::documents($cabinetId)->search()
    ->fullText('invoice')
    ->where('STATUS', 'open')
    ->whereDateBetween('DWSTOREDATETIME', $from, $to)
    ->latest('DWSTOREDATETIME')
    ->cursor()
    ->each(fn (DocumentData $document) => /* … */);

$document = DocuWare::documents($cabinetId)->find($documentId);

$document = DocuWare::documents($cabinetId)->store(
    fileContent: file_get_contents('invoice.pdf'),
    fileName: 'invoice.pdf',
    indexes: IndexFields::make()->text('STATUS', 'open')->number('AMOUNT', 42),
);

DocuWare::documents($cabinetId)->update($documentId, IndexFields::make()->text('STATUS', 'closed'));
$pdf = DocuWare::documents($cabinetId)->download($documentId, TargetFileType::PDF);
DocuWare::documents($cabinetId)->delete($documentId);
```

## Resources

Every resource is reached from the `DocuWare` facade (default instance) or from a resolved client
(`DocuWare::instance('acme')->…`, `DocuWare::connection($config)->…`). Each returns immutable `*Data`
objects or collections of them.

### Documents — `DocuWare::documents($cabinetId)`

| Method | Returns | Description |
| --- | --- | --- |
| `search()` | `SearchQuery` | Fluent search builder (see below) |
| `find($id)` | `DocumentData` | A single document |
| `count()` | `int` | Total documents in the cabinet |
| `store($fileContent, $fileName, $indexes, $storeDialogId)` | `DocumentData` | Create a data record |
| `update($id, $indexes, $forceUpdate = false)` | `Collection<DocumentFieldData>` | Update index values |
| `delete($id)` | — | Delete a document |
| `download($id, TargetFileType = AUTO, $keepAnnotations = false)` | `string` | Binary content |
| `preview($id)` | `string` | Preview image bytes |
| `sections($id)` / `section($sectionId)` | `Collection<SectionData>` / `SectionData` | Document sections |
| `deleteSection($sectionId)` | `bool` | Delete a section |
| `downloadSection($sectionId)` / `textshot($sectionId)` | `string` | Section data / text layer |
| `thumbnail($sectionId, $page = 0)` | `string` | Thumbnail bytes |
| `annotations($id)` / `annotate($id, $payload)` / `stamps($id)` | `Collection` / `mixed` / `Collection` | Annotations & stamps |
| `applicationProperties($id)` / `addApplicationProperties(...)` / `updateApplicationProperties(...)` / `deleteApplicationProperties(...)` | `mixed` | App-defined properties |
| `clip($ids, $force)` / `staple($ids, $force)` / `unclip($id)` / `unstaple($id)` | `DocumentData` / `DocumentPageData` | Content merge/divide |
| `transfer($id, $destinationFileCabinetId, $storeDialogId, $keepSource = false)` | `bool` | Move/copy across cabinets |
| `batchUpdate(...)` / `appendPdf(...)` / `appendFiles(...)` / `replaceSection(...)` | `array` / `Section` / `Document` | Batch & upload operations |
| `workflowHistory($id)` | `Collection<HistoryStepData>` | Document workflow history |

### Other resources

```php
DocuWare::fileCabinets()->all();                 // Collection<FileCabinetData>
DocuWare::fileCabinets()->info($cabinetId);      // FileCabinetInformationData
DocuWare::fileCabinets()->fields($cabinetId);    // Collection<FieldData>

DocuWare::dialogs($cabinetId)->all();            // Collection<DialogData>
DocuWare::dialogs($cabinetId)->ofType(DialogType::SEARCH);
DocuWare::dialogs($cabinetId)->find($dialogId);

DocuWare::selectLists($cabinetId)->get($dialogId, 'FIELD');
DocuWare::selectLists($cabinetId)->filtered($dialogId, 'FIELD', $expression);

DocuWare::organizations()->all();                // Collection<OrganizationData>
DocuWare::organizations()->loginToken($targetProducts, $usage, $lifetime);

DocuWare::users()->all();                        // Collection<UserData>
DocuWare::users()->find($userId);
DocuWare::users()->ofGroup($groupId);
DocuWare::users()->ofRole($roleId);
DocuWare::users()->create($user);                // UserInput
DocuWare::users()->update($user);
DocuWare::users()->addToGroup($userId, $groupIds);
DocuWare::users()->removeFromGroup($userId, $groupIds);
DocuWare::users()->addToRole($userId, $roleIds);
DocuWare::users()->removeFromRole($userId, $roleIds);
DocuWare::users()->groupsOf($userId);
DocuWare::users()->rolesOf($userId);

DocuWare::groups()->all();                       // Collection<GroupData>
DocuWare::roles()->all();                        // Collection<RoleData>

DocuWare::workflows()->historySteps($workflowId, $instanceId);  // InstanceHistoryData

DocuWare::trash()->search($page, $perPage, ...); // TrashPageData
DocuWare::trash()->delete($ids);                 // DeleteDocumentsData
DocuWare::trash()->restore($ids);                // RestoreDocumentsData
```

## Search builder

`DocuWare::documents($cabinetId)->search()` returns a fluent `SearchQuery`. Conditions and ordering
are chainable; a terminal method runs the request.

```php
DocuWare::documents($cabinetId)->search()
    ->fileCabinets([$cabinetId, $otherCabinetId])  // search across multiple cabinets
    ->dialog($searchDialogId)                       // optional explicit search dialog
    ->fullText('term')
    ->where('FIELD', 'value')
    ->whereIn('FIELD', ['a', 'b'])
    ->whereEmpty('FIELD')
    ->whereNotEmpty('FIELD')
    ->whereDate('DATE_FIELD', '>=', $carbon)
    ->whereDateBetween('DATE_FIELD', $from, $to)
    ->orderBy('FIELD', 'desc')        // or ->latest('FIELD') / ->oldest('FIELD')
    ->page(1)->perPage(50);
```

| Terminal | Returns | Description |
| --- | --- | --- |
| `get()` | `DocumentPageData` | One page of results |
| `first()` | `?DocumentData` | First match, or `null` |
| `count()` | `int` | Total matching documents |
| `cursor()` | `LazyCollection<DocumentData>` | Memory-safe iteration over **all** pages |

## Index field values

DocuWare expects each index value in a specific wire shape (`FieldName` / `Item` / `ItemElementName`).
You don't build that by hand — pass PHP values to the `IndexFields` builder and it converts each one to
the correct typed field. This is the answer to *"how do I pass an int / string / table and get the right
index field?"*

```php
use CodebarAg\DocuWare\Data\Write\IndexFields;
use Illuminate\Support\Carbon;

$indexes = IndexFields::make()
    ->text('STATUS', 'open')            // String
    ->memo('NOTES', 'long text…')       // Memo
    ->keyword('TAGS', 'invoice')        // Keyword
    ->number('AMOUNT', 42)              // Int
    ->decimal('PRICE', 19.99)           // Decimal
    ->date('DUE', Carbon::today())      // date  → YYYY-MM-DD
    ->dateTime('SEEN', Carbon::now())   // datetime → YYYY-MM-DD HH:MM:SS
    ->table('POSITIONS', [              // Table → rows of typed cells
        ['ARTICLE' => 'Widget', 'QTY' => 5],
        ['ARTICLE' => 'Gadget', 'QTY' => 3],
    ]);

DocuWare::documents($cabinetId)->store(
    fileContent: file_get_contents('invoice.pdf'),
    fileName: 'invoice.pdf',
    indexes: $indexes,
);

DocuWare::documents($cabinetId)->update($documentId, IndexFields::make()->number('AMOUNT', 99));
```

| Builder method | Argument type | DocuWare `ItemElementName` |
| --- | --- | --- |
| `text($name, $value)` | `?string` | `String` |
| `memo($name, $value)` | `?string` | `Memo` |
| `keyword($name, $value)` | `?string` | `Keyword` |
| `number($name, $value)` | `?int` | `Int` |
| `decimal($name, $value)` | `int\|float\|null` | `Decimal` |
| `date($name, $value)` | `?Carbon` | `String` (date) |
| `dateTime($name, $value)` | `?Carbon` | `String` (datetime) |
| `table($name, $rows)` | `array\|Collection` | `Table` |

`store()` and `update()` also accept a raw `Collection` of index DTOs if you need to build them
yourself, but `IndexFields` is the recommended path.

## Immutable data & withers

Every response is an immutable `spatie/laravel-data` object:

```php
$user    = DocuWare::users()->find($id);
$updated = $user->copyWith(active: false);   // a new instance; $user is untouched
$array   = $user->toArray();                 // serialization for free
```

## Enums

| Enum | Cases |
| --- | --- |
| `TargetFileType` | `AUTO`, `PDF`, `ORIGINAL` |
| `DialogType` | `SEARCH`, `STORE`, `RESULT`, `INDEX`, `LIST`, `FOLDERS` |
| `DocuWareFieldTypeEnum` | `STRING`, `INT`, `DECIMAL`, `DATE`, `DATETIME`, `TABLE` |
| `Grant` | `Credentials`, `TrustedUser`, `Token` |

## Encrypted document URLs

```php
$url = DocuWare::url($url, $username, $password, $passphrase)
    ->fileCabinet($cabinetId)
    ->document($documentId)
    ->validUntil($carbon)   // optional expiry
    ->make();
```

## Endpoint catalog

The resources above are the public API. Under the hood they send Saloon **requests** that map 1:1 to
DocuWare Platform REST endpoints. This catalog documents that internal mapping for transparency and
advanced use. Paths are relative to `<DOCUWARE_URL>/{platform_path}/` (default `DocuWare/Platform`).
**Cached** = the request implements Saloon's `Cacheable` (TTL from `configurations.cache.*`).

<details>
<summary><strong>All 68 endpoints across 25 groups</strong></summary>

### Authentication › OAuth

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `GetIdentityServiceConfiguration` | GET | `{identityServiceUrl}/.well-known/openid-configuration` | `IdentityServiceConfiguration` | yes | identityServiceUrl |
| `GetResponsibleIdentityService` | GET | `<url>/{platform_path}/Home/IdentityServiceInfo` | `ResponsibleIdentityService` | yes | url |
| `RequestTokenWithCredentials` | POST | `{tokenEndpoint}` | `RequestTokenDto` |  | tokenEndpoint, clientId, scope, username, password |
| `RequestTokenWithCredentialsTrustedUser` | POST | `{tokenEndpoint}` | `RequestTokenDto` |  | tokenEndpoint, clientId, scope, username, password, impersonateName |
| `RequestTokenWithDocuWareToken` | POST | `{tokenEndpoint}` | `RequestTokenDto` |  | tokenEndpoint, token, clientId, scope |

### Documents

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `GetDocumentPreviewRequest` | GET | `/FileCabinets/{fileCabinetId}/Documents/{documentId}/Image` | `string` | yes | fileCabinetId, documentId |

### Documents › ApplicationProperties

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `AddApplicationProperties` | POST | `/FileCabinets/{fileCabinetId}/Documents/{documentId}/DocumentApplicationProperties` | `mixed` | yes | fileCabinetId, documentId, properties |
| `DeleteApplicationProperties` | POST | `/FileCabinets/{fileCabinetId}/Documents/{documentId}/DocumentApplicationProperties` | `mixed` | yes | fileCabinetId, documentId, propertyNames |
| `GetApplicationProperties` | GET | `/FileCabinets/{fileCabinetId}/Documents/{documentId}/DocumentApplicationProperties` | `mixed` | yes | fileCabinetId, documentId |
| `UpdateApplicationProperties` | POST | `/FileCabinets/{fileCabinetId}/Documents/{documentId}/DocumentApplicationProperties` | `mixed` | yes | fileCabinetId, documentId, properties |

### Documents › ClipUnclipStapleUnstaple

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `Clip` | POST | `/FileCabinets/{documentTrayId}/Operations/ContentMerge` | `Document` |  | documentTrayId, documents, force |
| `Staple` | POST | `/FileCabinets/{documentTrayId}/Operations/ContentMerge` | `Document` |  | documentTrayId, documents, force |
| `Unclip` | POST | `/FileCabinets/{documentTrayId}/Operations/ContentDivide` | `DocumentPaginator` |  | documentTrayId, documentId |
| `Unstaple` | POST | `/FileCabinets/{documentTrayId}/Operations/ContentDivide` | `DocumentPaginator` |  | documentTrayId, documentId |

### Documents › DocumentsTrashBin

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `DeleteDocuments` | POST | `/TrashBin/BatchDelete` | `DeleteDocumentsDto` |  | ids |
| `GetDocuments` | POST | `/TrashBin/Query` | `TrashDocumentPaginator` | yes | page, perPage, searchTerm, orderField, orderDirection, condition, forceRefresh |
| `RestoreDocuments` | POST | `/TrashBin/BatchRestore` | `RestoreDocumentsDto` |  | ids |

### Documents › Download

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `DownloadDocument` | GET | `/FileCabinets/{fileCabinetId}/Documents/{documentId}/FileDownload` | `mixed` | yes | fileCabinetId, documentId, targetFileType, keepAnnotations |
| `DownloadSection` | GET | `/FileCabinets/{fileCabinetId}/Sections/{sectionId}/Data` | `mixed` | yes | fileCabinetId, sectionId |
| `DownloadThumbnail` | GET | `/FileCabinets/{fileCabinetId}/Rendering/{sectionId}/Thumbnail` | `mixed` | yes | fileCabinetId, sectionId, page |

### Documents › ModifyDocuments

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `DeleteDocument` | DELETE | `/FileCabinets/{fileCabinetId}/Documents/{documentId}` | `Response` |  | fileCabinetId, documentId |
| `TransferDocument` | POST | `?` | `bool` |  | fileCabinetId, destinationFileCabinetId, documentId, storeDialogId, fields, keepSource, fillIntellix, useDefaultDialog |

### Documents › Sections

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `DeleteSection` | DELETE | `/FileCabinets/{fileCabinetId}/Sections/{sectionId}` | `bool` | yes | fileCabinetId, sectionId |
| `GetASpecificSection` | GET | `/FileCabinets/{fileCabinetId}/Sections/{sectionId}` | `Section` | yes | fileCabinetId, sectionId |
| `GetAllSectionsFromADocument` | GET | `/FileCabinets/{fileCabinetId}/Sections` | `Collection` | yes | fileCabinetId, documentId |
| `GetTextshot` | GET | `/FileCabinets/{fileCabinetId}/Sections/{sectionId}/Textshot` | `mixed` | yes | fileCabinetId, sectionId |

### Documents › Stamps

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `AddDocumentAnnotations` | POST | `/FileCabinets/{fileCabinetId}/Documents/{documentId}/Annotation` | `array` |  | fileCabinetId, documentId, payload |
| `GetDocumentAnnotations` | GET | `/FileCabinets/{fileCabinetId}/Documents/{documentId}/Annotation` | `Collection` | yes | fileCabinetId, documentId |
| `GetStamps` | GET | `/FileCabinets/{fileCabinetId}/Stamps` | `Collection` |  | fileCabinetId |

### Documents › UpdateIndexValues

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `UpdateIndexValues` | PUT | `/FileCabinets/{fileCabinetId}/Documents/{documentId}/Fields` | `Collection` |  | fileCabinetId, documentId, indexes, forceUpdate |

### Fields

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `GetFieldsRequest` | GET | `/FileCabinets/{fileCabinetId}` | `Collection` | yes | fileCabinetId |

### FileCabinets › Batch

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `BatchDocumentsUpdateFields` | POST | `/FileCabinets/{fileCabinetId}/Operations/BatchDocumentsUpdateFields` | `array` |  | fileCabinetId, payload, contentType |

### FileCabinets › CheckInCheckOut

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `CheckInDocumentFromFileSystem` | POST | `/FileCabinets/{fileCabinetId}/Documents/{documentId}/CheckInFromFileSystem` | `Document` |  | fileCabinetId, documentId, checkInJson, fileContent, fileName |
| `CheckoutDocumentToFileSystem` | POST | `/FileCabinets/{fileCabinetId}/Documents/{documentId}/CheckoutToFileSystem` | `CheckoutToFileSystemResult` |  | fileCabinetId, documentId |
| `UndoDocumentCheckout` | PUT | `/FileCabinets/{fileCabinetId}/Operations/ProcessDocumentAction?DocId={documentId}` | `Document` |  | fileCabinetId, documentId |

### FileCabinets › Dialogs

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `GetASpecificDialog` | GET | `/FileCabinets/{fileCabinetId}/Dialogs/{dialogId}` | `mixed` | yes | fileCabinetId, dialogId |
| `GetAllDialogs` | GET | `/FileCabinets/{fileCabinetId}/Dialogs` | `mixed` | yes | fileCabinetId |
| `GetDialogsOfASpecificType` | GET | `/FileCabinets/{fileCabinetId}/Dialogs` | `mixed` | yes | fileCabinetId, dialogType |

### FileCabinets › General

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `GetFileCabinetInformation` | GET | `/FileCabinets/{fileCabinetId}` | `FileCabinetInformation` | yes | fileCabinetId |
| `GetTotalNumberOfDocuments` | GET | `/FileCabinets/{fileCabinetId}/Query/CountExpression` | `int` | yes | fileCabinetId, searchDialogId |

### FileCabinets › Search

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `GetASpecificDocumentFromAFileCabinet` | GET | `/FileCabinets/{fileCabinetId}/Documents/{documentId}` | `Document` | yes | fileCabinetId, documentId |
| `GetDocumentsFromAFileCabinet` | GET | `/FileCabinets/{fileCabinetId}/Documents` | `DocumentPaginator` | yes | fileCabinetId, fields, page, perPage |

### FileCabinets › SelectLists

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `GetFilteredSelectLists` | POST | `/FileCabinets/{fileCabinetId}/Query/SelectListExpression` | `mixed` | yes | fileCabinetId, dialogId, fieldName, dialogExpression |
| `GetSelectLists` | POST | `/FileCabinets/{fileCabinetId}/Query/SelectListExpression` | `mixed` | yes | fileCabinetId, dialogId, fieldName |

### FileCabinets › Upload

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `AppendASinglePDFToADocument` | POST | `/FileCabinets/{fileCabinetId}/Sections` | `Section` |  | fileCabinetId, documentId, fileContent, fileName |
| `AppendFilesToADataRecord` | POST | `/FileCabinets/{fileCabinetId}/Documents/{dataRecordId}` | `Document` |  | fileCabinetId, dataRecordId, files |
| `CreateDataRecord` | POST | `/FileCabinets/{fileCabinetId}/Documents` | `Document` |  | fileCabinetId, fileContent, fileName, indexes, storeDialogId |
| `ReplaceAPDFDocumentSection` | POST | `/FileCabinets/{fileCabinetId}/Sections/{sectionId}/Data` | `Section` |  | fileCabinetId, sectionId, fileContent, fileName |

### General › Organization

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `GetAllFileCabinetsAndDocumentTrays` | GET | `/FileCabinets` | `Collection` | yes | organizationId |
| `GetLoginToken` | POST | `/Organization/LoginToken` | `string` | yes | targetProducts, usage, lifetime |
| `GetOrganization` | GET | `/Organizations` | `Collection` | yes |  |

### General › UserManagement › CreateUpdateUsers

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `CreateUser` | POST | `/Organization/UserInfo` | `GetUser` | yes | user |
| `UpdateUser` | POST | `/Organization/UserInfo` | `User` | yes | user |

### General › UserManagement › GetModifyGroups

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `AddUserToAGroup` | PUT | `/Organization/UserGroups` | `Response` |  | userId, ids |
| `GetAllGroupsForASpecificUser` | GET | `/Organization/UserGroups` | `Collection` | yes | userId, name, active |
| `GetGroups` | GET | `/Organization/Groups` | `Collection` | yes | name, active |
| `RemoveUserFromAGroup` | PUT | `/Organization/UserGroups` | `Response` |  | userId, ids |

### General › UserManagement › GetModifyRoles

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `AddUserToARole` | PUT | `/Organization/UserRoles` | `Response` |  | userId, ids |
| `GetAllRolesForASpecificUser` | GET | `/Organization/UserRoles` | `Collection` | yes | userId, name, active, type |
| `GetRoles` | GET | `/Organization/Roles` | `Collection` | yes | name, active, type |
| `RemoveUserFromARole` | PUT | `/Organization/UserRoles` | `Response` |  | userId, ids |

### General › UserManagement › GetUsers

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `GetUserById` | GET | `/Organization/UserByID` | `User` | yes | userId |
| `GetUsers` | GET | `/Organization/Users` | `Collection` | yes | name, active |
| `GetUsersOfAGroup` | GET | `/Organization/GroupUsers` | `Collection` | yes | groupId |
| `GetUsersOfARole` | GET | `/Organization/RoleUsers` | `Collection` | yes | roleId, includeGroupUsers |

### Search

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `GetSearchRequest` | POST | `/FileCabinets/{fileCabinetId}/Query/DialogExpression` | `DocumentPaginator` | yes | fileCabinetId, dialogId, additionalFileCabinetIds, page, perPage, searchTerm, orderField, orderDirection, condition |

### Workflow

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `GetDocumentWorkflowHistory` | GET | `/FileCabinets/{fileCabinetId}/Documents/{documentId}/WorkflowHistory` | `Collection` | yes | fileCabinetId, documentId |
| `GetDocumentWorkflowHistorySteps` | GET | `/Workflows/{workflowId}/Instances/{workflowInstanceId}/History` | `InstanceHistory` | yes | workflowId, workflowInstanceId |

</details>

## Data object (DTO) reference

Responses are mapped into immutable `*Data` objects under `CodebarAg\DocuWare\Data`. The most-used
shapes and their properties:

<details>
<summary><strong>Document data objects</strong></summary>

### `DocumentData` — `Data\Documents\DocumentData`

| Property | Type | Notes |
| --- | --- | --- |
| `id` | `int` | Document identifier |
| `title` | `string` | Document title |
| `file_size` | `int` | Bytes |
| `total_pages` | `int` | Page count |
| `extension` | `?string` | File extension |
| `content_type` | `string` | MIME type |
| `file_cabinet_id` | `string` | Cabinet id |
| `created_at` / `updated_at` | `Carbon` | Timestamps |
| `fields` | `?Collection<DocumentFieldData>` | Index values keyed by field name |
| `sections` | `?Collection<SectionData>` | Document sections |
| `suggestions` | `?Collection<SuggestionFieldData>` | Index suggestions |
| `flags` | `?DocumentFlagsData` | Document flags |
| `checksum_info` | `?ChecksumInfoData` | Checksum metadata |
| `version` | `?DocumentVersionData` | Version info |
| `links` | `?Collection<LinkData>` | Hypermedia links |

### `DocumentFieldData` — `Data\Documents\DocumentFieldData`

| Property | Type | Notes |
| --- | --- | --- |
| `name` | `string` | Field name (e.g. `AMOUNT`) |
| `label` | `string` | Display label |
| `value` | `null\|int\|float\|Carbon\|string\|Collection` | Parsed to native type (table → `Collection<TableRowData>`) |
| `type` | `string` | Wire type (`String`, `Int`, `Decimal`, `Date`, `Table`, …) |
| `isNull` | `bool` | Value is null |
| `systemField` | `bool` | System vs user field |
| `readOnly` | `?bool` | Read-only flag |

### `DocumentPageData` — `Data\Documents\DocumentPageData`

| Property | Type |
| --- | --- |
| `total` / `perPage` / `currentPage` / `lastPage` / `from` / `to` | `int` |
| `documents` | `Collection<DocumentData>` |

### `FieldData` — `Data\Documents\FieldData`

| Property | Type | Notes |
| --- | --- | --- |
| `name` / `label` / `type` / `scope` | `string` | DB name, display name, field type, System/User |
| `length` / `precision` | `?int` | Size & decimal precision |
| `notEmpty` / `usedAsDocumentName` | `?bool` | Constraints |
| `tableFieldColumns` | `?array` | For table fields |

### `TableRowData` — `Data\Documents\TableRowData`

| Property | Type |
| --- | --- |
| `fields` | `Collection<string, DocumentFieldData>` (keyed by field name) |

</details>

<details>
<summary><strong>File cabinets, dialogs & sections</strong></summary>

### `FileCabinetInformationData` — `Data\FileCabinets\FileCabinetInformationData`

| Property | Type | Notes |
| --- | --- | --- |
| `id` / `name` / `color` | `string` | Identity |
| `isBasket` / `usable` / `default` | `bool` | Flags |
| `versionManagement` | `string` | Version management mode |
| `hasFullTextSupport` | `bool` | Full-text search enabled |
| `addIndexEntriesInUpperCase` | `bool` | Upper-cases index entries |
| `fields` | `?array` | Field metadata |

### `FileCabinetData` — `Data\Organization\FileCabinetData`

Lighter cabinet shape returned by `fileCabinets()->all()` (id, name, color, isBasket, usable, default…).

### `DialogData` — `Data\FileCabinets\DialogData`

| Property | Type | Notes |
| --- | --- | --- |
| `id` / `type` / `label` | `string` | Identity & dialog type |
| `isDefault` | `bool` | Default dialog |
| `fileCabinetId` | `string` | Owning cabinet |
| `fields` | `?array` | Raw fields; `fieldObjects()` returns typed `DialogFieldData` |

### `DialogFieldData` — `Data\FileCabinets\DialogFieldData`

`dbName`, `label`, `type`, `length`, `precision`, `locked`, `readOnly`, `notEmpty`, `visible`,
`usedAsDocumentName`, plus select-list metadata.

### `SectionData` — `Data\SectionData`

`id`, `contentType`, `pageCount`, `fileSize`, `originalFileName`, `haveMorePages`, `contentModified`,
`links`, `pages`, `thumbnails`.

</details>

<details>
<summary><strong>Organization & user management</strong></summary>

### `OrganizationData` — `Data\Organization\OrganizationData`

`id`, `name`, `guid`, `additionalInfo`, `configurationRights`, `isTwoStepVerificationEnabled`,
`isTwoStepVerificationRequired`, `links`.

### `UserData` — `Data\Users\UserData`

`id`, `name`, `firstName`, `lastName`, `email`, `dbName`, `active`, `isHighSecurity`,
`defaultWebBasket`, `outOfOffice`, `regionalSettings`, `twoStepVerificationEnabled`, `links`.

### `GroupData` — `Data\Users\GroupData`

`id`, `name`, `active`, `links`.

### `RoleData` — `Data\Users\RoleData`

`id`, `name`, `active`, `type`, `links`.

</details>

## Caching

`GET` metadata and search requests implement Saloon's `Cacheable` (see the **Cached** column in the
endpoint catalog). Cached responses use the `configurations.cache.driver` store with a TTL of
`configurations.cache.lifetime_in_seconds`. Set the lifetime to `0` to disable caching. Search requests
honor a per-call force-refresh; write operations are never cached.

## Error handling

Operations return data or throw — there is no error-carrying result object. Every package exception
descends from `DocuWareException`, so you can catch the base type to handle any DocuWare failure, or a
specific subclass for a specific status.

| Exception | When |
| --- | --- |
| `AuthenticationException` | 401 |
| `BadRequestException` | 400 |
| `ForbiddenException` | 403 |
| `NotFoundException` | 404 |
| `MethodNotAllowedException` | 405 |
| `ConflictException` | 409 |
| `ValidationException` | 422 |
| `RateLimitException` | 429 |
| `ConnectionException` | network/transport failure |
| `RequestException` / `DocuWareException` | any other 4xx / 5xx |

Each exception exposes redacted, structured context for logging and reporting:

| Member | Description |
| --- | --- |
| `statusCode` | HTTP status code |
| `instance` | Which DocuWare instance produced it |
| `docuwareMessage` | The DocuWare error message |
| `requestId` | Correlation id for support |
| `context()` | Full redacted context array |

Secrets are never present in any exception message or context.

## Events

```php
use CodebarAg\DocuWare\Events\{ResponseReceived, TokenRefreshed};
```

- `ResponseReceived` — a redacted snapshot (instance, method, path, status, request id). The body is
  attached only when `docuware.debug.capture_bodies` is enabled, and is redacted even then.
- `TokenRefreshed` — instance + url, no secret.

## Security

- **Structural redaction**: `Authorization`, passwords, tokens, cookies, and the passphrase are stripped
  from every log, event, and exception. An architecture test asserts no sentinel secret can leak.
- **Token store**: access tokens are `Crypt`-encrypted, namespaced per instance, and refreshed under a
  cache lock to avoid stampedes.

## What changed in v14 (and why)

v14 is a ground-up rewrite. The old surface is gone; here is what replaced it and why.

| Removed in ≤ v13 | Replaced by | Why |
| --- | --- | --- |
| `DocuWare` god-object & `$connector->send(new SomeRequest())` | `DocuWare` facade → resources (`DocuWare::documents($cabinet)->…`) | A small, domain-shaped API instead of leaking Saloon requests into app code |
| `Connectors\DocuWareConnector` (public) | Internal `Transport\DocuWareConnector`, one per resolved client | Connection lifecycle and auth are package concerns, not yours |
| Hand-rolled `DTO\*` classes | Immutable `spatie/laravel-data` `*Data` objects with `copyWith()` | Free serialization, validation, and immutability |
| `DocuWareSearchRequestBuilder` | Fluent `SearchQuery` (`->where()`, `->whereDateBetween()`, `->cursor()`) | Composable, lazy, memory-safe iteration over all pages |
| Manually built index-field arrays | `IndexFields::make()->text()/number()/date()/table()` | You pass PHP values; the package builds the correct wire format |
| Config-file-only multi-tenancy | Config instances **and** runtime `DocuWare::connection(InstanceConfig)` DTOs | Database-driven tenancy without touching config files |
| Per-operation error/result objects & mixed exceptions | One `DocuWareException` hierarchy | A single, predictable error model |
| `DocuWareResponseLog` / `DocuWareOAuthLog` events | `ResponseReceived` / `TokenRefreshed` (redacted) | Observability without leaking secrets |

There is no compatibility layer — rewrite call sites against the resources and data objects documented
above. The old → new mapping in this table plus the [Resources](#resources) section cover every
previously available operation.

## Testing

```bash
composer test          # offline (fixtures replay), the CI gate
composer test:live     # live integration suite against a real instance
composer analyse       # PHPStan (max)
composer format        # Pint
```

In your own app, fake DocuWare with Saloon's mock client, or assert against the recorded fixtures under
`tests/Fixtures/saloon`.

## Credits

- [Sebastian Bürgin-Fix](https://github.com/StanBarrows)
- [codebar Solutions AG](https://www.codebar.ch)
- [Ricoh Schweiz AG](https://www.ricoh.ch)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). See [License File](LICENSE.md) for more information.

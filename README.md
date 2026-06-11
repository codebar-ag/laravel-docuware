<img src="https://banners.beyondco.de/Laravel%20DocuWare.png?theme=light&packageManager=composer+require&packageName=codebar-ag%2Flaravel-docuware&pattern=circuitBoard&style=style_1&description=An+opinionated+way+to+integrate+DocuWare+with+Laravel&md=1&showWatermark=0&fontSize=175px&images=document-report">

[![Latest Version on Packagist](https://img.shields.io/packagist/v/codebar-ag/laravel-docuware.svg?style=flat-square)](https://packagist.org/packages/codebar-ag/laravel-docuware)
[![Total Downloads](https://img.shields.io/packagist/dt/codebar-ag/laravel-docuware.svg?style=flat-square)](https://packagist.org/packages/codebar-ag/laravel-docuware)
[![GitHub-Tests](https://github.com/codebar-ag/laravel-docuware/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/codebar-ag/laravel-docuware/actions/workflows/run-tests.yml)
[![GitHub Code Style](https://github.com/codebar-ag/laravel-docuware/actions/workflows/fix-php-code-style-issues.yml/badge.svg?branch=main)](https://github.com/codebar-ag/laravel-docuware/actions/workflows/fix-php-code-style-issues.yml)
[![PHPStan](https://github.com/codebar-ag/laravel-docuware/actions/workflows/phpstan.yml/badge.svg)](https://github.com/codebar-ag/laravel-docuware/actions/workflows/phpstan.yml)

An opinionated, modern integration for the [DocuWare Platform REST API](https://developer.docuware.com/rest/index.html).
You work with **resources** and **immutable data objects** — never raw HTTP.

> **2.0 is a ground-up rewrite.** Upgrading from 1.x? See [UPGRADE-2.0.md](UPGRADE-2.0.md).

## Highlights

- 🎯 **Small, domain-shaped API** — `DocuWare::documents($cabinet)->search()->cursor()`.
- 🧊 **Immutable data** via [`spatie/laravel-data`](https://spatie.be/docs/laravel-data) with `->copyWith(...)` withers.
- 🏢 **Multi-tenant first-class** — `DocuWare::instance('tenant')`.
- 🔒 **Secure by default** — secrets are never logged, evented, or thrown.
- ♻️ **Resilient** — encrypted + lock-guarded token store, retry with backoff honoring `Retry-After`.
- 🧯 **One error model** — every failure is a `DocuWareException`.
- 🧪 **Fully fakeable & typed** — PHPStan max, offline fixture replay.

## Requirements

| Version | PHP | Laravel |
| --- | --- | --- |
| 2.x | ^8.3 – ^8.5 | 12 / 13 |
| 1.x | ^8.2 | 11 / 12 |

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

Every resource is reached from the `DocuWare` facade and returns immutable `*Data` objects (or
collections of them).

### Documents — `DocuWare::documents($cabinetId)`

| Method | Description |
| --- | --- |
| `search()` | Fluent `SearchQuery` (see below) |
| `find($id)` | A single `DocumentData` |
| `count()` | Total documents in the cabinet |
| `store($content, $name, $indexes, $storeDialogId)` | Create a data record |
| `update($id, $indexes, $forceUpdate)` | Update index values |
| `delete($id)` | Delete a document |
| `download($id, TargetFileType, $keepAnnotations)` | Binary content |

#### Search

```php
DocuWare::documents($cabinetId)->search()
    ->fullText('term')
    ->where('FIELD', 'value')
    ->whereIn('FIELD', ['a', 'b'])
    ->whereEmpty('FIELD') ->whereNotEmpty('FIELD')
    ->whereDate('DATE_FIELD', '>=', $carbon)
    ->whereDateBetween('DATE_FIELD', $from, $to)
    ->orderBy('FIELD', 'desc')   // or ->latest('FIELD') / ->oldest('FIELD')
    ->page(1)->perPage(50);

$page  = $query->get();      // DocumentPageData (one page)
$first = $query->first();    // ?DocumentData
$total = $query->count();    // int
$all   = $query->cursor();   // LazyCollection<DocumentData> over all pages
```

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

DocuWare::users()->all();                        // Collection<UserData>
DocuWare::users()->find($userId);
DocuWare::users()->ofGroup($groupId);
DocuWare::users()->ofRole($roleId);

DocuWare::groups()->all();                       // Collection<GroupData>
DocuWare::roles()->all();                        // Collection<RoleData>

DocuWare::workflows()->historySteps($workflowId, $instanceId);

DocuWare::trash()->delete($ids);                 // DeleteDocumentsData
DocuWare::trash()->restore($ids);                // RestoreDocumentsData
```

### Encrypted document URLs

```php
$url = DocuWare::url($url, $username, $password, $passphrase)
    ->fileCabinet($cabinetId)
    ->document($documentId)
    ->make();
```

## Immutable data & withers

```php
$user    = DocuWare::users()->find($id);
$updated = $user->copyWith(active: false);   // a new instance; $user is untouched
$array   = $user->toArray();                 // serialization for free
```

## Multi-tenancy

Add named instances to `config/laravel-docuware.php`:

```php
'instances' => [
    'acme' => ['grant' => 'credentials', 'url' => '…', 'username' => '…', 'password' => '…'],
    'globex' => ['grant' => 'token', 'url' => '…', 'token' => '…'],
],
```

```php
DocuWare::instance('acme')->documents($cabinetId)->search()->get();
```

Each instance is isolated: its own connector, cache namespace, encrypted token entry, and limiter.
The three OAuth grants are supported: `credentials`, `trusted_user`, `token` (dwtoken).

## Error handling

Operations return data or throw — there is no error-carrying result object. Everything descends
from `DocuWareException`:

```php
use CodebarAg\DocuWare\Exceptions\{DocuWareException, NotFoundException, AuthenticationException, RateLimitException};

try {
    DocuWare::documents($cabinetId)->find($id);
} catch (NotFoundException $e) {
    // $e->statusCode, $e->instance, $e->docuwareMessage, $e->requestId, $e->context()
} catch (DocuWareException $e) {
    // any other DocuWare failure
}
```

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
| `RequestException` / `DocuWareException` | other 4xx / 5xx |

## Events

```php
use CodebarAg\DocuWare\Events\{ResponseReceived, TokenRefreshed};
```

- `ResponseReceived` — redacted snapshot (instance, method, path, status, request id). The body is
  attached only when `docuware.debug.capture_bodies` is enabled, and is redacted even then.
- `TokenRefreshed` — instance + url, no secret.

## Security

- **Structural redaction**: `Authorization`, passwords, tokens, cookies and the passphrase are
  stripped from every log, event, and exception. An arch test asserts no sentinel secret can leak.
- **Token store**: access tokens are `Crypt`-encrypted, namespaced per instance, and refreshed
  under a cache lock to avoid stampedes.

## Testing

```bash
composer test          # offline (fixtures replay), the CI gate
composer test:live     # live integration suite against a real instance
composer analyse       # PHPStan (max)
composer format        # Pint
```

In your own app, fake DocuWare with Saloon's mock client, or assert against the recorded fixtures
under `tests/Fixtures/saloon`.

## Credits

- [Sebastian Bürgin-Fix](https://github.com/StanBarrows)
- [codebar Solutions AG](https://www.codebar.ch)
- [Ricoh Schweiz AG](https://www.ricoh.ch)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). See [License File](LICENSE.md) for more information.

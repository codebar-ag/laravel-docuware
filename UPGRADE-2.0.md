# Upgrading to laravel-docuware 2.0

2.0 is a **ground-up rewrite** with a new public API. It is a deliberate major release — there
is no automatic backward compatibility with 1.x. This guide maps the old surface to the new one.

## TL;DR

- You no longer touch Saloon connectors or request classes. Everything goes through the
  **`DocuWare` facade → resources → data objects**.
- Configuration gains a first-class **`instances`** map for multi-tenancy. Single-app users keep
  using the same `DOCUWARE_*` env vars (they map onto the `default` instance).
- Responses are immutable **`spatie/laravel-data`** objects (`DocumentData`, `UserData`, …).
- All failures throw one hierarchy rooted at **`DocuWareException`**.
- Secrets are never logged, evented, or thrown.

## Requirements

- PHP 8.3–8.5, Laravel 12/13.
- New dependency: `spatie/laravel-data` (installed automatically).

## Configuration

Re-publish the config (`php artisan vendor:publish --tag=laravel-docuware-config`) or merge these keys:

```php
'default' => env('DOCUWARE_INSTANCE', 'default'),

'instances' => [
    'default' => [
        'grant'     => env('DOCUWARE_GRANT', 'credentials'), // credentials | trusted_user | token
        'url'       => env('DOCUWARE_URL'),
        'username'  => env('DOCUWARE_USERNAME'),
        'password'  => env('DOCUWARE_PASSWORD'),
        'passphrase'=> env('DOCUWARE_PASSPHRASE'),
        // ...
    ],
],

'retry'      => [...],   // exponential backoff, honors Retry-After
'rate_limit' => [...],   // optional, per instance
'debug'      => ['capture_bodies' => false],
```

The legacy flat `credentials` / `passphrase` / `configurations` keys still resolve the `default`
instance, so an existing `.env` keeps working.

## API mapping

| 1.x | 2.0 |
| --- | --- |
| `DocuWare::searchRequestBuilder()->fileCabinet($c)->fulltext($t)->get()` then `$connector->send(...)` | `DocuWare::documents($c)->search()->fullText($t)->get()` |
| iterate pages manually | `DocuWare::documents($c)->search()->cursor()` (lazy, all pages) |
| `new GetASpecificDocumentFromAFileCabinet($c, $id)` → `$connector->send()->dto()` | `DocuWare::documents($c)->find($id)` |
| `new CreateDataRecord($c, $content, $name, $indexes)` | `DocuWare::documents($c)->store($content, $name, $indexes)` |
| `new UpdateIndexValues($c, $id, $indexes)` | `DocuWare::documents($c)->update($id, $indexes)` |
| `new DeleteDocument($c, $id)` | `DocuWare::documents($c)->delete($id)` |
| `new DownloadDocument($c, $id, $type)` | `DocuWare::documents($c)->download($id, $type)` |
| `new GetOrganization()` | `DocuWare::organizations()->all()` |
| `new GetAllFileCabinetsAndDocumentTrays()` | `DocuWare::fileCabinets()->all()` |
| `new GetFieldsRequest($c)` | `DocuWare::fileCabinets()->fields($c)` |
| `new GetAllDialogs($c)` | `DocuWare::dialogs($c)->all()` |
| `new GetUsers()` / `GetUserById($id)` | `DocuWare::users()->all()` / `->find($id)` |
| `new GetGroups()` / `GetRoles()` | `DocuWare::groups()->all()` / `DocuWare::roles()->all()` |
| trash delete/restore requests | `DocuWare::trash()->delete($ids)` / `->restore($ids)` |
| `DocuWare::url(...)` | unchanged |

### Index values

```php
// 1.x: build a Collection of Index*DTO by hand
// 2.0:
use CodebarAg\DocuWare\Data\Write\IndexFields;

$indexes = IndexFields::make()
    ->text('STATUS', 'open')
    ->number('AMOUNT', 42)
    ->date('DUE_DATE', now());

DocuWare::documents($cabinet)->store($content, 'invoice.pdf', $indexes);
```

## Errors

```php
// 1.x: a mix of typed exceptions and an ErrorBag / fromFailed() hybrid on paginators.
// 2.0: operations return data or throw — one hierarchy.
use CodebarAg\DocuWare\Exceptions\NotFoundException;
use CodebarAg\DocuWare\Exceptions\DocuWareException;

try {
    DocuWare::documents($cabinet)->find($id);
} catch (NotFoundException $e) {
    // $e->statusCode, $e->instance, $e->docuwareMessage, $e->requestId, $e->context()
} catch (DocuWareException $e) {
    // any DocuWare failure
}
```

`DocumentPaginator`/`TrashDocumentPaginator` `->failed()` / `ErrorBag` are gone — a failed page
throws instead of returning an error-carrying object.

## Events

`DocuWareResponseLog` (which carried the entire raw `Response`) is **removed**. Listen instead to:

- `ResponseReceived` — redacted snapshot (instance, method, path, status, request id; body only
  when `debug.capture_bodies` is on, still redacted).
- `TokenRefreshed` — instance + url, no secret.

## Multi-tenancy

```php
DocuWare::instance('tenant-acme')->documents($cabinet)->search()->get();
```

Each instance has its own connector, cache namespace, encrypted token entry, and rate limiter.

## Removed / internal

- The `Connectors\DocuWareConnector`, `Requests\*`, and `Responses\*` namespaces are **internal**
  (`Transport\*`) and should not be referenced directly.
- Hand-rolled `DTO\*` classes are replaced by `Data\*` (`spatie/laravel-data`).
- The vestigial pre-OAuth cookie helper `Support\Auth` is removed.

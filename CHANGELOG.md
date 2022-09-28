# Changelog

All notable changes to `laravel-docuware` will be documented in this file.

## 1.2.2 - 2022-09-27
- Added `DOCUWARE_COOKIES` property to the config.php. This allows the user to manually set the DocuWare Request cookie instead of regenerating it after every cache reset. There is currently a DocuWare limitation with available seats per license, which can cause issues if you renew the request cookie too many times.  The current lifespan of a DocuWare cookie is one year.
- Added `DOCUWARE_CACHE_DRIVER` property to the config.php. This allows the user to manually set the default Cache Driver, which is used to store the DocuWare Request Cookie.

- Removed Solutions for Errors (Facade/Ignition).

## 1.1.0 - 2021-07-22

- Added `error` property to the `DocumentPaginator`. This is used for failed
  requests otherwise it is null. When the request fails for any reason an 
  ErrorBag is added with more information. Example:

```php 
CodebarAg\DocuWare\DTO\DocumentPaginator {
  ...
  +error: CodebarAg\DocuWare\DTO\ErrorBag {
    +code: 422
    +message: "'00000000-0000-0000-0000-0000000000000' is not valid cabinet id"
  }
}
```

## 1.0.0 - 2021-07-14

⚠️ This release introduces breaking changes. Update with caution ⚠️

- Stable release.
- **[Breaking Change]**: Searching in multiple file cabinets have been changed.
  The search no longer supports `additionalFileCabinets()`.  Please use
  `fileCabinets()` instead. Example:

```php
$paginator = DocuWare::search()
    ->fileCabinet('id-first')
    ->additionalFileCabinets(['id-second'])
    ->get();
```

Changed to:

```php
$paginator = DocuWare::search()
    ->fileCabinets(['id-first', 'id-second'])
    ->get();
```

## 0.7.0 - 2021-06-30

- Added valid until date for the encrypted URL.

## 0.6.0 - 2021-06-23

- Added feature to create encrypted URL.
- Added new environment variable for the passphrase `DOCUWARE_PASSPHRASE`.

## 0.5.0 - 2021-06-21

- Added feature to upload document with index values.

## 0.4.0 - 2021-05-17

- The default cookie lifetime changed to 1 year.
- Added nullable fields for the search.

## 0.3.1 - 2021-04-21

- It is no longer required to set the dialog to search documents.

## 0.3.0 - 2021-04-20

⚠️ This release introduces breaking changes. Update with caution ⚠️

- **[Breaking Change]** Changed DOCUWARE_USER environment to DOCUWARE_USERNAME
  for a clear labelling
- **[Breaking Change]** Changed DocumentPaginator property *items* to *documents*
  for a clear labelling

## 0.2.0 - 2021-04-19

- Authentication is completely handled by the package now. No need to login
  (`DocuWare::login`) or logout (`DocuWare::logout`).
- Added new environment variable **cookie_lifetime** to overwrite the lifetime 
  of the authentication cookie.

## 0.1.0 - 2021-04-06

- DTO fake methods added

## 0.0.0 - 2021-04-06

- initial release

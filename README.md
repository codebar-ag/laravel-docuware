<img src="https://banners.beyondco.de/Laravel%20DocuWare.png?theme=light&packageManager=composer+require&packageName=codebar-ag%2Flaravel-docuware&pattern=circuitBoard&style=style_1&description=An+opinionated+way+to+integrate+DocuWare+with+Laravel&md=1&showWatermark=0&fontSize=175px&images=document-report">

[![Latest Version on Packagist](https://img.shields.io/packagist/v/codebar-ag/laravel-docuware.svg?style=flat-square)](https://packagist.org/packages/codebar-ag/laravel-docuware)
[![Total Downloads](https://img.shields.io/packagist/dt/codebar-ag/laravel-docuware.svg?style=flat-square)](https://packagist.org/packages/codebar-ag/laravel-docuware)
[![run-tests](https://github.com/codebar-ag/laravel-docuware/actions/workflows/run-tests.yml/badge.svg)](https://github.com/codebar-ag/laravel-docuware/actions/workflows/run-tests.yml)
[![Check & fix styling](https://github.com/codebar-ag/laravel-docuware/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/codebar-ag/laravel-docuware/actions/workflows/php-cs-fixer.yml)
[![Psalm](https://github.com/codebar-ag/laravel-docuware/actions/workflows/psalm.yml/badge.svg)](https://github.com/codebar-ag/laravel-docuware/actions/workflows/psalm.yml)


This package was developed to give you a quick start to communicate with the
DocuWare REST API. It is used to query the most common endpoints.

⚠️ This package is not designed as a replacement of the official 
[DocuWare REST API](https://developer.docuware.com/rest/index.html).
See the documentation if you need further functionality. ⚠️

## 💡 What is DocuWare?

DocuWare provides cloud document management and workflow automation software
that enables you to digitize, secure and work with business documents,
then optimize the processes that power the core of your business.

## 🛠 Requirements

- PHP: `^8.0`
- Laravel: `^8.12`
- DocuWare Cloud Access

## ⚙️ Installation

You can install the package via composer:

```bash
composer require codebar-ag/laravel-docuware
```

Add the following environment variables to your `.env` file:

```bash
DOCUWARE_URL=https://domain.docuware.cloud
DOCUWARE_USER=user@domain.test
DOCUWARE_PASSWORD=password
```

## 🏗 Usage

```php
use CodebarAg\DocuWare\Facades\DocuWare;

/**
 * Login with your credentials. You only need to login once. Afterwards the
 * authentication cookie is stored in the cache `docuware.cookies` and is
 * used for all further requests.
 */
DocuWare::login();

/**
 * Logout your current session. Removes the authentication cookie in the cache.
 */
DocuWare::logout();

/**
 * Return all file cabinets.
 */
$cabinets = DocuWare::getFileCabinets();

/**
 * Return all fields of a file cabinet.
 */
$fields = DocuWare::getFields($fileCabinetId);

/**
 * Return all dialogs of a file cabinet.
 */
$dialogs = DocuWare::getDialogs($fileCabinetId);

/**
 * Return all used values for a specific field.
 */
$values = DocuWare::getSelectList($fileCabinetId, $dialogId, $fieldName);

/**
 * Return a document.
 */
$document = DocuWare::getDocument($fileCabinetId, $documentId);

/**
 * Return image preview of a document.
 */
$content = DocuWare::getDocumentPreview($fileCabinetId, $documentId);

/**
 * Download single document.
 */
$content = DocuWare::downloadDocument($fileCabinetId, $documentId);

/**
 * Download multiple documents.
 */
$content = DocuWare::downloadDocuments($fileCabinetId, $documentIds);

/**
 * Update value of a indexed field.
 */
$value = DocuWare::updateDocumentValue($fileCabinetId, $documentId, $fieldName, $newValue);

/**
 * Upload new document.
 */
$document = DocuWare::uploadDocument($fileCabinetId, $fileContent, $fileName);

/**
 * Delete document.
 */
DocuWare::deleteDocument($fileCabinetId, $documentId);
```

## 🔍 Search usage

```php
use CodebarAg\DocuWare\Facades\DocuWare;

/**
 * Most basic example to search for documents.
 */
$paginator = DocuWare::search()
    ->fileCabinet($fileCabinetId)
    ->dialog($dialogId)
    ->get();

/**
 * Search in multiple file cabinets.
 */
$paginator = DocuWare::search()
    ->fileCabinet($fileCabinetId)
    ->dialog($dialogId)
    ->additionalFileCabinets($additionalFileCabinets)
    ->get();

/**
 * Find results on the next page. 
 * Default: 1
 */
$paginator = DocuWare::search()
    ->fileCabinet($fileCabinetId)
    ->dialog($dialogId)
    ->page(2)
    ->get();
    
/**
 * Define the number of results which should be shown per page. 
 * Default: 50
 */
$paginator = DocuWare::search()
    ->fileCabinet($fileCabinetId)
    ->dialog($dialogId)
    ->perPage(30)
    ->get();

/**
 * Use the full-text search. You have to activate full-text search in your file
 * cabinet before you can use this feature.
 */
$paginator = DocuWare::search()
    ->fileCabinet($fileCabinetId)
    ->dialog($dialogId)
    ->fulltext('My secret document')
    ->get();

/**
 * Search documents which are created from the first of march.
 */
$paginator = DocuWare::search()
    ->fileCabinet($fileCabinetId)
    ->dialog($dialogId)
    ->dateFrom(Carbon::create(2021, 3, 1))
    ->get();

/**
 * Search documents which are created until the first of april.
 */
$paginator = DocuWare::search()
    ->fileCabinet($fileCabinetId)
    ->dialog($dialogId)
    ->dateUntil(Carbon::create(2021, 4, 1))
    ->get();

/**
 * Order the results by field name. Possibly values: 'desc' or 'asc'
 */
$paginator = DocuWare::search()
    ->fileCabinet($fileCabinetId)
    ->dialog($dialogId)
    ->orderBy('DWSTOREDATETIME', 'desc')
    ->get();

/**
 * Search documents filtered to the value. You can specify multiple filters.
 */
$paginator = DocuWare::search()
    ->fileCabinet($fileCabinetId)
    ->dialog($dialogId)
    ->filter('TYPE', 'Order')
    ->filter('OTHER_FIELD', 'other')
    ->get();
    
/**
 * Or combine all together.
 */
$paginator = DocuWare::search()
    ->fileCabinet($fileCabinetId)
    ->dialog($dialogId)
    ->additionalFileCabinets($additionalFileCabinets)
    ->page(2)
    ->perPage(30)
    ->fulltext('My secret document')
    ->dateFrom(Carbon::create(2021, 3, 1))
    ->dateUntil(Carbon::create(2021, 4, 1))
    ->filter('TYPE', 'Order')
    ->filter('OTHER_FIELD', 'other')
    ->orderBy('DWSTOREDATETIME', 'desc')
    ->get();
```

Please see [Tests](tests/Feature/DocuWareTest.php) for more details.

## ✨ Events

Following events are fired:

```php 
use CodebarAg\DocuWare\Events\DocuWareResponseLog;

// Log each response from the DocuWare REST API.
DocuWareResponseLog::class => [
    //
],
```

## 🔧 Configuration file

You can publish the config file with:
```bash
php artisan vendor:publish --provider="CodebarAg\DocuWare\DocuWareServiceProvider" --tag="docuware-config"
```

This is the contents of the published config file:

```php
return [

    /*
    |--------------------------------------------------------------------------
    | DocuWare credentials
    |--------------------------------------------------------------------------
    |
    | These values are used to connect your application with DocuWare.
    |
    */

    'url' => env('DOCUWARE_URL'),
    'user' => env('DOCUWARE_USER'),
    'password' => env('DOCUWARE_PASSWORD'),

];
```

## 🚧 Testing

Copy your own phpunit.xml-file.
```bash
cp phpunit.xml.dist phpunit.xml
```

Modify environment variables in the phpunit.xml-file:
```xml
<env name="DOCUWARE_URL" value="https://domain.docuware.cloud"/>
<env name="DOCUWARE_USER" value="user@domain.test"/>
<env name="DOCUWARE_PASSWORD" value="password"/>
```

Run the tests:
```bash
composer test
```

## 📝 Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## ✏️ Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## 🧑‍💻 Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## 🙏 Credits

- [Ruslan Steiger](https://github.com/SuddenlyRust)
- [All Contributors](../../contributors)
- [Skeleton Repository from Spatie](https://github.com/spatie/package-skeleton-laravel)
- [Laravel Package Training from Spatie](https://spatie.be/videos/laravel-package-training)

## 🎭 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

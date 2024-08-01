<img src="https://banners.beyondco.de/Laravel%20DocuWare.png?theme=light&packageManager=composer+require&packageName=codebar-ag%2Flaravel-docuware&pattern=circuitBoard&style=style_1&description=An+opinionated+way+to+integrate+DocuWare+with+Laravel&md=1&showWatermark=0&fontSize=175px&images=document-report">

[![Latest Version on Packagist](https://img.shields.io/packagist/v/codebar-ag/laravel-docuware.svg?style=flat-square)](https://packagist.org/packages/codebar-ag/laravel-docuware)
[![GitHub-Tests](https://github.com/codebar-ag/laravel-docuware/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/codebar-ag/laravel-docuware/actions/workflows/run-tests.yml)
[![GitHub Code Style](https://github.com/codebar-ag/laravel-docuware/actions/workflows/fix-php-code-style-issues.yml/badge.svg?branch=main)](https://github.com/codebar-ag/laravel-docuware/actions/workflows/fix-php-code-style-issues.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/codebar-ag/laravel-docuware.svg?style=flat-square)](https://packagist.org/packages/codebar-ag/laravel-docuware)

This package was developed to give you a quick start to communicate with the
DocuWare REST API. It is used to query the most common endpoints.

⚠️ This package is not designed as a replacement of the official
[DocuWare REST API](https://developer.docuware.com/rest/index.html).
See the documentation if you need further functionality. ⚠️

## Navigation
<!-- TOC -->
  * [💡 What is DocuWare?](#-what-is-docuware)
  * [🛠 Requirements](#-requirements)
  * [⚙️ Installation](#-installation)
  * [🏗 Usage](#-usage)
    * [Getting Started with OAuth](#getting-started-with-oauth)
    * [Getting a new token via Username & Password:](#getting-a-new-token-via-username--password)
    * [Getting a new token via Username & Password (Trusted User):](#getting-a-new-token-via-username--password-trusted-user)
    * [Available Requests](#available-requests)
  * [Extending the connector (EXAMPLE)](#extending-the-connector-example)
      * [Create a new connector](#create-a-new-connector)
      * [Use the new connector](#use-the-new-connector)
  * [🖼 Make encrypted URLs](#-make-encrypted-urls)
  * [🏋️ Document Index Fields DTO showcase](#-document-index-fields-dto-showcase)
  * [📦 Caching requests](#-caching-requests)
  * [💥 Exceptions explained](#-exceptions-explained)
  * [✨ Events](#-events)
  * [🔧 Configuration file](#-configuration-file)
  * [🚧 Testing](#-testing)
  * [📝 Changelog](#-changelog)
  * [✏️ Contributing](#-contributing)
  * [🧑‍💻 Security Vulnerabilities](#-security-vulnerabilities)
  * [🙏 Credits](#-credits)
  * [🎭 License](#-license)
<!-- TOC -->

## 💡 What is DocuWare?

DocuWare provides cloud document management and workflow automation software
that enables you to digitize, secure and work with business documents,
then optimize the processes that power the core of your business.

## 🛠 Requirements

<details>
 <summary>Version Support</summary>

| Version         | PHP Version | Laravel Version | DocuWare Cloud Access |
|-----------------|-------------|-----------------|-----------------------|
| > v11.0 (alpha) | ^8.2        | ^11.*           | ✅                     |
| > v4.0          | ^8.2        | ^11.*           | ✅                     |
| > v3.0          | ^8.2        | ^10.*           | ✅                     |
| > v2.0          | ^8.1        | ^9.*            | ✅                     |
| > v1.2          | ^8.1        | ^9.*            | ✅                     |
| < v1.2          | ^8.0        | ^8.*            | ✅                     |

</details>

<details>
 <summary>Current Support</summary>

| Group                               | Request                                                     | Supported | TODO |
|-------------------------------------|-------------------------------------------------------------|-----------|------|
| Authentication/OAuth                | 1. Get Responsible Identity Service                         | ✅         |      |
| Authentication/OAuth                | 2. Get Identity Service Configuration                       | ✅         |      |
| Authentication/OAuth                | 3.a Request Token w/ Username & Password                    | ✅         |      |
| Authentication/OAuth                | 3.b Request Token w/ a DocuWare Token                       | 🕣        |      |
| Authentication/OAuth                | 3.c Request Token w/ Username & Password (Trusted User)     | 🕣        |      |
| Authentication/OAuth                | 3.d.1 Obtain Windows Authorization (On Premises Only)       | 🕣        |      |
| Authentication/OAuth                | 3.d.2 Request Token /w a Windows Account (On Premises Only) | 🕣        |      |
| General/Organisation                | Get Login Token                                             | ✅         |      |
| General/Organisation                | Get Organization                                            | ✅         |      |
| General/Organisation                | Get All File Cabinets and Document Trays                    | ✅         |      |
| General/UserManagement              | Get Users by ID                                             | ✅         |      |
| General/UserManagement              | Get Users of a Role                                         | ✅         |      |
| General/UserManagement              | Get Users of a Group                                        | ✅         |      |
| General/UserManagement              | Create User                                                 | ✅         |      |
| General/UserManagement              | Update User                                                 | ✅         |      |
| General/UserManagement              | Get Groups                                                  | ✅         |      |
| General/UserManagement              | Get All Groups for a Specific User                          | ✅         |      |
| General/UserManagement              | Add User to a Group                                         | ✅         |      |
| General/UserManagement              | Remove User from a Group                                    | ✅         |      |
| General/UserManagement              | Get Roles                                                   | ✅         |      |
| General/UserManagement              | Get All Roles for a Specific User                           | ✅         |      |
| General/UserManagement              | Add User to a Role                                          | ✅         |      |
| General/UserManagement              | Remove User from a Role                                     | ✅         |      |
| FileCabinets/General                | Get File Cabinet Information                                | ✅         |      |
| FileCabinets/General                | Get Total Number of Documents                               | ✅         |      |
| FileCabinets/Dialogs                | Get All Dialogs                                             | ✅         |      |
| FileCabinets/Dialogs                | Get a Specific Dialog                                       | ✅         |      |
| FileCabinets/Dialogs                | Get Dialogs of a Specific Type                              | ✅         |      |
| FileCabinets/Search                 | Get Documents from a File Cabinet                           | ✅         |      |
| FileCabinets/Search                 | Get a Specific Document From a File Cabinet                 | ✅         |      |
| FileCabinets/Search                 | Search for Documents in a Single File Cabinet               | ✅         |      |
| FileCabinets/Search                 | Search for Documents in Multiple File Cabinets              | ✅         |      |
| FileCabinets/CheckInCheckOut        | Check-out & Download a Document                             | 🕣        |      |
| FileCabinets/CheckInCheckOut        | Check-in a Document from the File System                    | 🕣        |      |
| FileCabinets/CheckInCheckOut        | Undo Check-out                                              | 🕣        |      |
| FileCabinets/SelectLists            | Get Select Lists & Get Filtered Select Lists                | ✅         |      |
| FileCabinets/Upload                 | Create Data Record                                          | ✅         |      |
| FileCabinets/Upload                 | Append File(s) to a Data Record                             | ✅         |      |
| FileCabinets/Upload                 | Upload a Single File for a Data Record                      | ❌         | -    |
| FileCabinets/Upload                 | Create a Data Record & Upload File                          | ❌         | -    |
| FileCabinets/Upload                 | Create Data Record & Upload File Using Store Dialog         | ❌         | -    |
| FileCabinets/Upload                 | Append a Single PDF to a Document                           | ❌         | -    |
| FileCabinets/Upload                 | Replace a PDF Document Section                              | ❌         | -    |
| FileCabinets/BatchIndexFieldsUpdate | Batch Update Index Fields By Id                             | ❌         | -    |
| FileCabinets/BatchIndexFieldsUpdate | Batch Update Index Fields By Search                         | ❌         | -    |
| FileCabinets/BatchIndexFieldsUpdate | Batch Append/Update Keyword Fields By Id                    | ❌         | -    |
| Documents/UpdateIndexValues         | Update Index Values                                         | ✅         |      |
| Documents/UpdateIndexValues         | Update Table Field Values                                   | ❌         | - ?  |
| Documents/ModifyDocuments           | Transfer Document                                           | ✅         |      |
| Documents/ModifyDocuments           | Delete Document                                             | ✅         |      |
| Documents/ClipUnclip&StapleUnstaple | Clip                                                        | ✅         |      |
| Documents/ClipUnclip&StapleUnstaple | Unclip                                                      | ✅         |      |
| Documents/ClipUnclip&StapleUnstaple | Staple                                                      | ✅         |      |
| Documents/ClipUnclip&StapleUnstaple | Unstaple                                                    | ✅         |      |
| Documents/AnnotationsStamps         | AddStampWithPosition                                        | 🕣        |      |
| Documents/AnnotationsStamps         | AddStampWithBestPosition                                    | 🕣        |      |
| Documents/AnnotationsStamps         | AddTextAnnotation                                           | 🕣        |      |
| Documents/AnnotationsStamps         | AddRectEntryAnnotation                                      | 🕣        |      |
| Documents/AnnotationsStamps         | AddLineEntryAnnotation                                      | 🕣        |      |
| Documents/AnnotationsStamps         | AddPolyLineEntryAnnotation                                  | ❌         | -    |
| Documents/AnnotationsStamps         | DeleteAnnotation                                            | ❌         | -    |
| Documents/AnnotationsStamps         | UpdateTextAnnotation                                        | 🕣        |      |
| Documents/AnnotationsStamps         | Get Stamps                                                  | ❌         | -    |
| Documents/DocumentsTrashBin         | Get Documents                                               | ✅         |      |
| Documents/DocumentsTrashBin         | Delete Documents                                            | ✅         |      |
| Documents/DocumentsTrashBin         | Restore Documents                                           | ✅         |      |
| Documents/ApplicationProperties     | Get Application Properties                                  | ✅         |      |
| Documents/ApplicationProperties     | Add Application Properties                                  | ✅         |      |
| Documents/ApplicationProperties     | Delete Application Properties                               | ✅         |      |
| Documents/ApplicationProperties     | Update Application Properties                               | ✅         |      |
| Documents/Sections                  | Get All Sections from a Document                            | ✅         |      |
| Documents/Sections                  | Get a Specific Section                                      | ✅         |      |
| Documents/Sections                  | Delete Section                                              | ✅         |      |
| Documents/Download                  | Download Document                                           | ✅         |      |
| Documents/Download                  | Download Section                                            | ✅         |      |
| Documents/Download                  | Download Thumbnail                                          | ✅         |      |
| Workflow                            | Get Document Workflow History                               | ✅         |      |
| Workflow                            | Get Document Workflow History Steps                         | ✅         |      |

</details>


## ⚙️ Installation

You can install the package via composer:

```bash
composer require codebar-ag/laravel-docuware
```

Add the following environment variables to your `.env` file:

```bash
DOCUWARE_URL=https://domain.docuware.cloud
DOCUWARE_USERNAME=user@domain.test
DOCUWARE_PASSWORD=password
DOCUWARE_PASSPHRASE="passphrase"
```

With the passphrase we are able to encrypt the URLs.

⚠️ You need to escape backslashes in your passphrase with another backslash:

```bash 
# ❌ Passphrase contains a backslash and is not escaped:
DOCUWARE_PASSPHRASE="a#bcd>2~C1'abc\#"

# ✅ We need to escape the backslash with another backslash:
DOCUWARE_PASSPHRASE="a#bcd>2~C1'abc\\#"
```

## 🏗 Usage

### Getting Started with OAuth
> This package automatically handles the generation of OAuth token for you and stores them in cache.

### Getting a new token via Username & Password:

```php
use CodebarAg\DocuWare\Connectors\DocuWareConnector;
use CodebarAg\DocuWare\DTO\Config\ConfigWithCredentials;

$connector = new DocuWareConnector(
    configuration: new ConfigWithCredentials(
        username: 'username',
        password: 'password',
    )
);
```

### Getting a new token via Username & Password (Trusted User):

```php
use CodebarAg\DocuWare\Connectors\DocuWareConnector;
use CodebarAg\DocuWare\DTO\Config\ConfigWithCredentialsTrustedUser;

$connector = new DocuWareConnector(
    configuration: new ConfigWithCredentialsTrustedUser(
        username: 'username',
        password: 'password',
        impersonatedUsername: 'impersonatedUsername',
    )
);
```

### Available Requests

- [General](docs/General)
  - [Organisation](docs/General/organization.md)
  - [User Management](docs/General/User%20Management)
    - [Get Users](docs/General/User%20Management/get_users.md)
    - [Create/Update Users](docs/General/User%20Management/create-update_users.md)
    - [Get/Modify Groups](docs/General/User%20Management/get-modify_groups.md)
    - [Get/Modify Roles](docs/General/User%20Management/get-modify_roles.md)
- [File Cabinets](docs/File%20Cabinets)
  - [General](docs/File%20Cabinets/general.md)
  - [Dialogs](docs/File%20Cabinets/dialogs.md)
  - [Search](docs/File%20Cabinets/search.md)
  - [Check/In & Check/Out](docs/File%20Cabinets/check-in_check-out.md)
  - [Select Lists](docs/File%20Cabinets/select_lists.md)
  - [Upload](docs/File%20Cabinets/upload.md)
  - [Batch Index Fields Update](docs/File%20Cabinets/batch_index_fields_update.md)
- [Documents](docs/Documents)
  - [Update Index Values](docs/Documents/update_index_values.md)
  - [Modify Documents](docs/Documents/modify_documents.md)
  - [Clip/Unclip & Staple/Unstaple](docs/Documents/clip-unclicp_and_staple-unstaple.md)
  - [Annotations & Stamps](docs/Documents/annotations-stamps.md)
  - [Documents Trash Bin](docs/Documents/documents-trash-bin.md)
  - [Application Properties](docs/Documents/application_properties.md)
  - [Sections](docs/Documents/sections.md)
  - [Download](docs/Documents/download.md)
- [Workflow](docs/workflow.md)



## Extending the connector (EXAMPLE)

> We understand it may be repetitive to pass the configuration every time you create a new connector.
>
> You can extend the connector and set the configuration once.

#### Create a new connector

```php
<?php

namespace App\Connectors;

use CodebarAg\DocuWare\Connectors\DocuWareConnector;
use CodebarAg\DocuWare\DTO\Config\ConfigWithCredentials;

class YourOwnDocuWareConnector extends DocuWareConnector
{
    public function __construct() {
        $configuration = new ConfigWithCredentials(
            username: 'username',
            password: 'password',
        );
    
        parent::__construct($configuration);
    }
}
```

#### Use the new connector

```php
use App\Connectors\YourOwnDocuWareConnector;
use CodebarAg\DocuWare\DTO\Config\ConfigWithCredentials;

$connector = new YourOwnDocuWareConnector();
```

## 🖼 Make encrypted URLs

- [Encrypted URLs](docs/encrypted_urls.md)

## 🏋️ Document Index Fields DTO showcase

- [Document Index Fields DTO](docs/dto.md)

## 📦 Caching requests

- [Caching Requests](docs/caching.md)

## 💥 Exceptions explained

- [Exceptions](docs/exceptions.md)


## ✨ Events

> The Following events will be fired:

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
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cache driver
    |--------------------------------------------------------------------------
    | You may like to define a different cache driver than the default Laravel cache driver.
    |
    */

    'cache_driver' => env('DOCUWARE_CACHE_DRIVER', env('CACHE_DRIVER', 'file')),

    /*
    |--------------------------------------------------------------------------
    | Requests timeout
    |--------------------------------------------------------------------------
    | This variable is optional and only used if you want to set the request timeout manually.
    |
    */

    'timeout' => env('DOCUWARE_TIMEOUT', 15),

    /*
    |--------------------------------------------------------------------------
    | DocuWare Credentials
    |--------------------------------------------------------------------------
    |
    | Before you can communicate with the DocuWare REST-API it is necessary
    | to enter your credentials. You should specify a url containing the
    | scheme and hostname. In addition add your username and password.
    |
    */

    'credentials' => [
        'url' => env('DOCUWARE_URL'),
        'username' => env('DOCUWARE_USERNAME'),
        'password' => env('DOCUWARE_PASSWORD'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Passphrase
    |--------------------------------------------------------------------------
    |
    | In order to create encrypted URLs we need a passphrase. This enables a
    | secure exchange of DocuWare URLs without anyone being able to modify
    | your query strings. You can find it in the organization settings.
    |
    */

    'passphrase' => env('DOCUWARE_PASSPHRASE'),

    /*
    |--------------------------------------------------------------------------
    | Configurations
    |--------------------------------------------------------------------------
    |
    */
    'configurations' => [
        'search' => [
            'operation' => 'And',

            /*
             * Force Refresh
             * Determine if result list is retrieved from the cache when ForceRefresh is set
             * to false (default) or always a new one is executed when ForceRefresh is set to true.
             */

            'force_refresh' => true,
            'include_suggestions' => false,
            'additional_result_fields' => [],
        ],
        'cache' => [
            'driver' => env('DOCUWARE_CACHE_DRIVER', env('CACHE_DRIVER', 'file')),
            'lifetime_in_seconds' => env('DOCUWARE_CACHE_LIFETIME_IN_SECONDS', 60),
        ],
    ],
];
```

## 🚧 Testing

Copy your own phpunit.xml-file.

```bash
cp phpunit.xml.dist phpunit.xml
```

Modify environment variables in the phpunit.xml-file:

```xml
<env name="DOCUWARE_TOKEN" value=""/>
<env name="DOCUWARE_URL" value="https://domain.docuware.cloud"/>
<env name="DOCUWARE_USERNAME" value="user@domain.test"/>
<env name="DOCUWARE_PASSWORD" value="password"/>
<env name="DOCUWARE_PASSPHRASE" value="passphrase"/>
<env name="DOCUWARE_TIMEOUT" value="30"/>
<env name="DOCUWARE_CACHE_LIFETIME_IN_SECONDS" value="0"/>

<env name="DOCUWARE_TESTS_FILE_CABINET_ID" value=""/>
<env name="DOCUWARE_TESTS_DIALOG_ID" value=""/>
<env name="DOCUWARE_TESTS_BASKET_ID" value=""/>
<env name="DOCUWARE_TESTS_ORGANIZATION_ID" value=""/>
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

- [Sebastian Fix](https://github.com/StanBarrows)
- [Rhys Lees](https://github.com/RhysLees)
- [All Contributors](../../contributors)
- [Skeleton Repository from Spatie](https://github.com/spatie/package-skeleton-laravel)
- [Laravel Package Training from Spatie](https://spatie.be/videos/laravel-package-training)

## 🎭 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

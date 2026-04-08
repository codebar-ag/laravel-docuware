<img src="https://banners.beyondco.de/Laravel%20DocuWare.png?theme=light&packageManager=composer+require&packageName=codebar-ag%2Flaravel-docuware&pattern=circuitBoard&style=style_1&description=An+opinionated+way+to+integrate+DocuWare+with+Laravel&md=1&showWatermark=0&fontSize=175px&images=document-report">

[![Latest Version on Packagist](https://img.shields.io/packagist/v/codebar-ag/laravel-docuware.svg?style=flat-square)](https://packagist.org/packages/codebar-ag/laravel-docuware)
[![Total Downloads](https://img.shields.io/packagist/dt/codebar-ag/laravel-docuware.svg?style=flat-square)](https://packagist.org/packages/codebar-ag/laravel-docuware)
[![GitHub-Tests](https://github.com/codebar-ag/laravel-docuware/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/codebar-ag/laravel-docuware/actions/workflows/run-tests.yml)
[![GitHub Code Style](https://github.com/codebar-ag/laravel-docuware/actions/workflows/fix-php-code-style-issues.yml/badge.svg?branch=main)](https://github.com/codebar-ag/laravel-docuware/actions/workflows/fix-php-code-style-issues.yml)
[![PHPStan](https://github.com/codebar-ag/laravel-docuware/actions/workflows/phpstan.yml/badge.svg)](https://github.com/codebar-ag/laravel-docuware/actions/workflows/phpstan.yml)
[![Dependency Review](https://github.com/codebar-ag/laravel-docuware/actions/workflows/dependency-review.yml/badge.svg)](https://github.com/codebar-ag/laravel-docuware/actions/workflows/dependency-review.yml)

This package was developed to give you a quick start to communicate with the
DocuWare REST API. It is used to query the most common endpoints.

⚠️ This package is not designed as a replacement of the official
[DocuWare REST API](https://developer.docuware.com/rest/index.html).
See the documentation if you need further functionality. ⚠️

## Navigation
<!-- TOC -->
  * [Navigation](#navigation)
  * [💡 What is DocuWare?](#-what-is-docuware)
  * [🛠 Requirements](#-requirements)
  * [⚙️ Installation](#-installation)
  * [🏗 Usage](#-usage)
    * [Getting Started with OAuth](#getting-started-with-oauth)
    * [Getting a new token via Username & Password:](#getting-a-new-token-via-username--password)
    * [Getting a new token via Username & Password (Trusted User):](#getting-a-new-token-via-username--password-trusted-user)
    * [Available Requests](#available-requests)
      * [Organization](#organization)
        * [Get Organization](#get-organization)
        * [Get All File Cabinets And Document Trays](#get-all-file-cabinets-and-document-trays)
      * [User Management](#user-management)
        * [Get Users](#get-users)
          * [Get Users](#get-users-1)
          * [Get User By Id](#get-user-by-id)
          * [Get Users Of A Role](#get-users-of-a-role)
          * [Get Users Of A Group](#get-users-of-a-group)
        * [Create/Update Users](#createupdate-users)
          * [Create User](#create-user)
          * [Update User](#update-user)
        * [Get/Modify Groups](#getmodify-groups)
          * [Get Groups](#get-groups)
          * [Get All Groups For A Specific User](#get-all-groups-for-a-specific-user)
          * [Add User To A Group](#add-user-to-a-group)
          * [Remove User From A Group](#remove-user-from-a-group)
        * [Get/Modify Roles](#getmodify-roles)
          * [Get Roles](#get-roles)
          * [Get All Roles For A Specific User](#get-all-roles-for-a-specific-user)
          * [Add User To A Role](#add-user-to-a-role)
          * [Remove User From A Role](#remove-user-from-a-role)
      * [File Cabinets](#file-cabinets)
        * [General](#general)
          * [Get File Cabinet Information](#get-file-cabinet-information)
          * [Get Total Number Of Documents](#get-total-number-of-documents)
        * [Dialogs](#dialogs)
          * [Get All Dialogs](#get-all-dialogs)
          * [Get Dialogs of a Specific Type](#get-dialogs-of-a-specific-type)
          * [Get Dialogs Of A Specific Type](#get-dialogs-of-a-specific-type-1)
        * [Search](#search)
          * [Get A Specific Document From A File Cabinet](#get-a-specific-document-from-a-file-cabinet)
          * [Get Documents From A File Cabinet](#get-documents-from-a-file-cabinet)
          * [Most basic example to search for documents.](#most-basic-example-to-search-for-documents)
          * [Search in multiple file cabinets](#search-in-multiple-file-cabinets)
          * [Find results on the next page](#find-results-on-the-next-page)
          * [Define the number of results which should be shown per page](#define-the-number-of-results-which-should-be-shown-per-page)
          * [Use the full-text search](#use-the-full-text-search)
          * [Search documents which are created from the first of march.](#search-documents-which-are-created-from-the-first-of-march)
          * [Search documents which are created until the first of april.](#search-documents-which-are-created-until-the-first-of-april)
          * [Order the results by field name.](#order-the-results-by-field-name)
          * [Search documents filtered to the value.](#search-documents-filtered-to-the-value)
          * [Search documents filtered to multiple values.](#search-documents-filtered-to-multiple-values)
          * [Search documents with empty or non-empty index fields.](#search-documents-with-empty-or-non-empty-index-fields)
          * [You can specify the dialog which should be used.](#you-can-specify-the-dialog-which-should-be-used)
          * [You can also combine everything.](#you-can-also-combine-everything)
        * [Check In Check Out](#check-in-check-out)
        * [Select Lists](#select-lists)
          * [Get Select Lists](#get-select-lists)
        * [Upload](#upload)
          * [Create Data Record](#create-data-record)
          * [Create Table Data Record](#create-table-data-record)
          * [Append File(s) To A Data Record](#append-files-to-a-data-record)
          * [Append A Single PDF To A Document](#append-a-single-pdf-to-a-document)
          * [Replace A PDF Document Section](#replace-a-pdf-document-section)
          * [Batch Index Fields Update](#batch-index-fields-update)
          * [Get Fields](#get-fields)
      * [Documents](#documents)
        * [Update Index Values](#update-index-values)
          * [Update Table Data Record](#update-table-data-record)
        * [Modify Documents](#modify-documents)
          * [Transfer Document](#transfer-document)
          * [Delete Documents](#delete-documents)
        * [Clip/Unclip & Staple/Unstaple](#clipunclip--stapleunstaple)
          * [Clip](#clip)
          * [Unclip](#unclip)
          * [Staple](#staple)
          * [Unstaple](#unstaple)
        * [Annotations/Stamps](#annotationsstamps)
          * [Documents Trash Bin](#documents-trash-bin)
          * [Get Documents](#get-documents)
          * [Delete Documents](#delete-documents-1)
          * [Restore Documents](#restore-documents)
        * [Application Properties](#application-properties)
          * [Add Application Properties](#add-application-properties)
          * [Update Application Properties](#update-application-properties)
          * [Delete Application Properties](#delete-application-properties)
          * [Get Application Properties](#get-application-properties)
        * [Sections](#sections)
          * [Get All Sections](#get-all-sections)
          * [Get Specific Section](#get-specific-section)
          * [Delete Section](#delete-section)
          * [Get Textshot](#get-textshot)
        * [Download](#download)
          * [Download Document](#download-document)
          * [Download Section](#download-section)
          * [Download Thumbnail](#download-thumbnail)
      * [Workflow](#workflow)
        * [Workflow History](#workflow-history)
          * [Get Document Workflow History](#get-document-workflow-history)
          * [Get Document Workflow History Steps](#get-document-workflow-history-steps)
  * [Extending the connector (EXAMPLE)](#extending-the-connector-example)
      * [Create a new connector](#create-a-new-connector)
      * [Use the new connector](#use-the-new-connector)
  * [🖼 Make encrypted URLs](#-make-encrypted-urls)
    * [Make encrypted URL for a document in a file cabinet.](#make-encrypted-url-for-a-document-in-a-file-cabinet)
    * [Make encrypted URL for a document in a basket.](#make-encrypted-url-for-a-document-in-a-basket)
    * [Make encrypted URL valid for a specific amount of time.](#make-encrypted-url-valid-for-a-specific-amount-of-time)
  * [🏋️ Document Index Fields DTO showcase](#-document-index-fields-dto-showcase)
  * [📦 Caching requests](#-caching-requests)
    * [Is Cached](#is-cached)
    * [Invalidate Cache](#invalidate-cache)
    * [Disable Caching](#disable-caching)
  * [💥 Exceptions explained](#-exceptions-explained)
  * [✨ Events](#-events)
  * [🔧 Configuration file](#-configuration-file)
  * [Postman collection parity & Saloon fixtures](#postman-collection-parity--saloon-fixtures)
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

| Version       | PHP Version | Laravel Version | DocuWare Cloud Access |
|---------------|-------------|-----------------|-----------------------|
| v12.0         | ^8.2 - ^8.4 | 12.*            | ✅                     |
| v11.0 (alpha) | ^8.2        | 11.*            | ✅                     |
| > v4.0        | ^8.2        | 11.*            | ✅                     |
| > v3.0        | ^8.2        | 10.*            | ✅                     |
| > v2.0        | ^8.1        | 9.*             | ✅                     |
| > v1.2        | ^8.1        | 9.*             | ✅                     |
| < v1.2        | ^8.0        | 8.*             | ✅                     |

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
| FileCabinets/CheckInCheckOut        | Check-out & Download a Document                             | ✅         | `CheckoutDocumentToFileSystem` |
| FileCabinets/CheckInCheckOut        | Check-in a Document from the File System                    | ✅         | `CheckInDocumentFromFileSystem` |
| FileCabinets/CheckInCheckOut        | Undo Check-out                                              | ✅         | `UndoDocumentCheckout` |
| FileCabinets/SelectLists            | Get Select Lists & Get Filtered Select Lists                | ✅         |      |
| FileCabinets/Upload                 | Create Data Record                                          | ✅         |      |
| FileCabinets/Upload                 | Append File(s) to a Data Record                             | ✅         |      |
| FileCabinets/Upload                 | Upload a Single File for a Data Record                      | ✅         | `CreateDataRecord` (multipart `POST …/Documents`) |
| FileCabinets/Upload                 | Create a Data Record & Upload File                          | ✅         | `CreateDataRecord` |
| FileCabinets/Upload                 | Create Data Record & Upload File Using Store Dialog         | ✅         | `CreateDataRecord` + `storeDialogId` |
| FileCabinets/Upload                 | Append a Single PDF to a Document                           | ✅         | -    |
| FileCabinets/Upload                 | Replace a PDF Document Section                              | ✅         |      |
| FileCabinets/BatchIndexFieldsUpdate | Batch Update Index Fields By Id                             | ✅         | `BatchDocumentsUpdateFields` |
| FileCabinets/BatchIndexFieldsUpdate | Batch Update Index Fields By Search                         | ✅         | `BatchDocumentsUpdateFields` |
| FileCabinets/BatchIndexFieldsUpdate | Batch Append/Update Keyword Fields By Id                    | ✅         | `BatchDocumentsUpdateFields` |
| Documents/UpdateIndexValues         | Update Index Values                                         | ✅         |      |
| Documents/UpdateIndexValues         | Update Table Field Values                                   | ✅         | `UpdateIndexValues` + `IndexTableDTO` |
| Documents/ModifyDocuments           | Transfer Document                                           | ✅         |      |
| Documents/ModifyDocuments           | Delete Document                                             | ✅         |      |
| Documents/ClipUnclip&StapleUnstaple | Clip                                                        | ✅         |      |
| Documents/ClipUnclip&StapleUnstaple | Unclip                                                      | ✅         |      |
| Documents/ClipUnclip&StapleUnstaple | Staple                                                      | ✅         |      |
| Documents/ClipUnclip&StapleUnstaple | Unstaple                                                    | ✅         |      |
| Documents/AnnotationsStamps         | AddStampWithPosition                                        | ✅         | `AddDocumentAnnotations` |
| Documents/AnnotationsStamps         | AddStampWithBestPosition                                    | ✅         | `AddDocumentAnnotations` |
| Documents/AnnotationsStamps         | AddTextAnnotation                                           | ✅         | `AddDocumentAnnotations` |
| Documents/AnnotationsStamps         | AddRectEntryAnnotation                                      | ✅         | `AddDocumentAnnotations` |
| Documents/AnnotationsStamps         | AddLineEntryAnnotation                                      | ✅         | `AddDocumentAnnotations` |
| Documents/AnnotationsStamps         | AddPolyLineEntryAnnotation                                  | ✅         | `AddDocumentAnnotations` |
| Documents/AnnotationsStamps         | DeleteAnnotation                                            | ❌         | —    |
| Documents/AnnotationsStamps         | UpdateTextAnnotation                                        | ❌         | —    |
| Documents/AnnotationsStamps         | Get Stamps                                                  | ✅         | `GetStamps` |
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
| Documents/Sections/Textshot         | Get Textshot for a Specific Section                         | ✅         |      |
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
DOCUWARE_TIMEOUT=30
DOCUWARE_CACHE_DRIVER=file
DOCUWARE_CACHE_LIFETIME_IN_SECONDS=60
DOCUWARE_CLIENT_ID=docuware.platform.net.client
DOCUWARE_SCOPE=docuware.platform
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

### Enums

The package provides several enums to ensure type safety and consistency when working with DocuWare API values.

#### ConnectionEnum

Represents different connection types for DocuWare authentication:

```php
use CodebarAg\DocuWare\Enums\ConnectionEnum;

ConnectionEnum::WITHOUT_COOKIE;    
ConnectionEnum::STATIC_COOKIE; 
ConnectionEnum::DYNAMIC_COOKIE;
```

#### DialogType

Represents different types of dialogs in DocuWare:

```php
use CodebarAg\DocuWare\Enums\DialogType;

DialogType::SEARCH; 
DialogType::STORE;
DialogType::RESULT;
DialogType::INDEX; 
DialogType::LIST; 
DialogType::FOLDERS; 
```

#### DocuWareFieldTypeEnum

Represents different field types used in DocuWare document indexing:

```php
use CodebarAg\DocuWare\Enums\DocuWareFieldTypeEnum;

DocuWareFieldTypeEnum::STRING;
DocuWareFieldTypeEnum::INT; 
DocuWareFieldTypeEnum::DECIMAL;
DocuWareFieldTypeEnum::DATE;
DocuWareFieldTypeEnum::DATETIME;
DocuWareFieldTypeEnum::TABLE;
```

### Available Requests

The following sections provide examples for each available request type. All functionality is documented inline below with code examples.

#### Organization

| Request                                                     | Supported |
|-------------------------------------------------------------|-----------|
| Get Login Token                                             | ✅         |
| Get Organization                                            | ✅         |
| Get All File Cabinets and Document Trays                    | ✅         |


##### Get Organization
```php
use CodebarAg\DocuWare\Requests\General\Organization\GetOrganization;

$organizations = $this->connector->send(new GetOrganization())->dto();
```

##### Get All File Cabinets And Document Trays
```php
use CodebarAg\DocuWare\Requests\General\Organization\GetAllFileCabinetsAndDocumentTrays;

$cabinetsAndTrays = $this->connector->send(new GetAllFileCabinetsAndDocumentTrays())->dto();
```

#### User Management

##### Get Users

| Request              | Supported |
|----------------------|-----------|
| Get Users            | ✅         |
| Get Users by ID      | ✅         |
| Get Users of a Role  | ✅         |
| Get Users of a Group | ✅         |

###### Get Users
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers\GetUsers;

$users = $this->connector->send(new GetUsers())->dto();
```

###### Get User By Id
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers\GetUserById;

$user = $this->connector->send(new GetUserById($userId))->dto();
```

###### Get Users Of A Role
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers\GetUsersOfARole;

$users = $this->connector->send(new GetUsersOfARole($roleId))->dto();
```

###### Get Users Of A Group
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers\GetUsersOfAGroup;

$users = $this->connector->send(new GetUsersOfAGroup($groupId))->dto();
```

##### Create/Update Users

| Request     | Supported |
|-------------|-----------|
| Create User | ✅         |
| Update User | ✅         |

###### Create User
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\CreateUpdateUsers\CreateUser;

$user = $connector->send(new CreateUser(new User(
    name: $timestamp.' - Test User',
    dbName: $timestamp,
    email: $timestamp.'-test@example.test',
    password: 'TESTPASSWORD',
)))->dto();
```

###### Update User
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\CreateUpdateUsers\UpdateUser;

$user->name .= ' - Updated';
$user->active = false;

$user = $connector->send(new UpdateUser($user))->dto();
```

##### Get/Modify Groups

| Request                            | Supported |
|------------------------------------|-----------|
| Get Groups                         | ✅         |
| Get All Groups for a Specific User | ✅         |
| Add User to a Group                | ✅         |
| Remove User from a Group           | ✅         |

###### Get Groups
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyGroups\GetGroups;

$groups = $connector->send(new GetGroups())->dto();
```

###### Get All Groups For A Specific User
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyGroups\GetAllGroupsForASpecificUser;

$groups = $connector->send(new GetAllGroupsForASpecificUser($userId))->dto();
```

###### Add User To A Group
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyGroups\AddUserToAGroup;

$response = $connector->send(new AddUserToAGroup(
    userId: $userId,
    ids: [$groupId],
))->dto();
```

###### Remove User From A Group
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyGroups\RemoveUserFromAGroup;

$response = $connector->send(new RemoveUserFromAGroup(
    userId: $userId,
    ids: [$groupId],
))->dto();
```

##### Get/Modify Roles

| Request                           | Supported |
|-----------------------------------|-----------|
| Get Roles                         | ✅         |
| Get All Roles for a Specific User | ✅         |
| Add User to a Role                | ✅         |
| Remove User from a Role           | ✅         |

###### Get Roles
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyRoles\GetRoles;

$roles = $this->connector->send(new GetRoles())->dto();
```

###### Get All Roles For A Specific User
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyRoles\GetAllRolesForASpecificUser;

$roles = $connector->send(new GetAllRolesForASpecificUser($userId))->dto();
```

###### Add User To A Role
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyRoles\AddUserToARole;

$response = $connector->send(new AddUserToARole(
    userId: $userId,
    ids: [$roleId],
))->dto();
```

###### Remove User From A Role
```php
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyRoles\RemoveUserFromARole;

$response = $connector->send(new RemoveUserFromARole(
    userId: $userId,
    ids: [$roleId],
))->dto();
```

#### File Cabinets

##### General

| Request                       | Supported |
|-------------------------------|-----------|
| Get File Cabinet Information  | ✅         |
| Get Total Number of Documents | ✅         |

###### Get File Cabinet Information
```php
use CodebarAg\DocuWare\Requests\FileCabinets\General\GetFileCabinetInformation;

$fileCabinet = $connector->send(new GetFileCabinetInformation($fileCabinetId))->dto();
```

###### Get Total Number Of Documents
```php
use CodebarAg\DocuWare\Requests\FileCabinets\General\GetTotalNumberOfDocuments;

$count = $connector->send(new GetTotalNumberOfDocuments(
    $fileCabinetId,
    $dialogId
))->dto();
```

##### Dialogs

| Request                        | Supported |
|--------------------------------|-----------|
| Get All Dialogs                | ✅         |
| Get a Specific Dialog          | ✅         |
| Get Dialogs of a Specific Type | ✅         |

###### Get All Dialogs
```php
use CodebarAg\DocuWare\Requests\FileCabinets\Dialogs\GetAllDialogs;

$dialogs = $connector->send(new GetAllDialogs($fileCabinetId))->dto();
```

###### Get Dialogs of a Specific Type
```php
use CodebarAg\DocuWare\Requests\FileCabinets\Dialogs\GetASpecificDialog;

$dialog = $connector->send(new GetASpecificDialog($fileCabinetId, $dialogId))->dto();
```

###### Get Dialogs Of A Specific Type
```php
use CodebarAg\DocuWare\Enums\DialogType;
use CodebarAg\DocuWare\Requests\FileCabinets\Dialogs\GetDialogsOfASpecificType;

$dialogs = $connector->send(new GetDialogsOfASpecificType($fileCabinetId, DialogType::SEARCH))->dto();
```

##### Search

| Description                                    | Implemented |
|------------------------------------------------|-------------|
| Get Documents from a File Cabinet              | ✅           |
| Get a Specific Document From a File Cabinet    | ✅           |
| Search for Documents in a Single File Cabinet  | ✅           |
| Search for Documents in Multiple File Cabinets | ✅           |

###### Get A Specific Document From A File Cabinet
```php
use CodebarAg\DocuWare\Requests\FileCabinets\Search\GetASpecificDocumentFromAFileCabinet;

$document = $connector->send(new GetASpecificDocumentFromAFileCabinet(
    $fileCabinetId,
    $documentId
))->dto();
```

######  Get Documents From A File Cabinet
```php
use CodebarAg\DocuWare\Requests\FileCabinets\Search\GetDocumentsFromAFileCabinet;

$documents = $connector->send(new GetDocumentsFromAFileCabinet(
    $fileCabinetId
))->dto();
```

###### Most basic example to search for documents.
> You only need to provide a valid file cabinet id.
```php
$fileCabinetId = '87356f8d-e50c-450b-909c-4eaccd318fbf';

$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($fileCabinetId)
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

###### Search in multiple file cabinets
> Provide an array of file cabinet ids.
```php
$fileCabinetIds = [
    '0ee72de3-4258-4353-8020-6a3ff6dd650f',
    '3f9cb4ff-82f2-44dc-b439-dd648269064f',
];

$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinets($fileCabinetIds)
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

###### Find results on the next page
> Default: 1
```php
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->page(2)
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

###### Define the number of results which should be shown per page
> Default: 50
```php
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->perPage(30)
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

###### Use the full-text search
> You have to activate full-text search in your file cabinet before you can use this feature.
```php 
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->fulltext('My secret document')
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

###### Search documents which are created from the first of march.
```php 
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->filterDate('DWSTOREDATETIME', '>=', Carbon::create(2021, 3, 1))
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

###### Search documents which are created until the first of april.
```php 
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->filterDate('DWSTOREDATETIME', '<', Carbon::create(2021, 4, 1))
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

###### Order the results by field name.
> Supported values: 'asc', 'desc'
```php
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->orderBy('DWSTOREDATETIME', 'desc')
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

###### Search documents filtered to the value.
> You can specify multiple filters.
```php 
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->filter('TYPE', 'Order')
    ->filter('OTHER_FIELD', 'other')
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

###### Search documents filtered to multiple values.
```php 
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->filterIn('TYPE', ['Order', 'Invoice'])
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

###### Search documents with empty or non-empty index fields.
> Use the **database field name** for `$name` (often uppercase in DocuWare), not the dialog display label. These map to DocuWare dialog expressions `EMPTY()` and `NOTEMPTY()`—they are not passed through `filter()` string quoting.

```php
// Documents where STATUS has no index value (DocuWare EMPTY())
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->filterEmpty('STATUS')
    ->get();

$paginator = $connector->send($paginatorRequest)->dto();
```

```php
// Documents where STATUS has any value (DocuWare NOTEMPTY())
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->filterNotEmpty('STATUS')
    ->get();

$paginator = $connector->send($paginatorRequest)->dto();
```

###### You can specify the dialog which should be used.
```php 
$dialogId = 'bb42c30a-89fc-4b81-9091-d7e326caba62';

$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->dialog($dialogId)
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

###### You can also combine everything.
```php  
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->page(2)
    ->perPage(30)
    ->fulltext('My secret document')
    ->filterDate('DWSTOREDATETIME', '>=', Carbon::create(2021, 3, 1))
    ->filterDate('DWSTOREDATETIME','<',Carbon::create(2021, 4, 1))
    ->filter('TYPE', 'Order')
    ->filter('OTHER_FIELD', 'other')
    ->orderBy('DWSTOREDATETIME', 'desc')
    ->dialog($dialogId)
    ->get();

$paginator = $connector->send($paginatorRequest)->dto();
```


##### Check In Check Out

| Request                                                     | Supported |
|-------------------------------------------------------------|-----------|
| Check-out & Download a Document                             | ✅         |
| Check-in a Document from the File System                    | ✅         |
| Undo Check-out                                              | ✅         |

> Implemented as `CheckoutDocumentToFileSystem`, `CheckInDocumentFromFileSystem`, and `UndoDocumentCheckout`. Your file cabinet must have **version management** enabled; otherwise DocuWare returns HTTP 405.

##### Select Lists
| Request                                      | Supported |
|----------------------------------------------|-----------|
| Get Select Lists & Get Filtered Select Lists | ✅         |

###### Get Select Lists
```php
use CodebarAg\DocuWare\Requests\FileCabinets\SelectLists\GetSelectLists;

$types = $this->connector->send(new GetSelectLists(
    $fileCabinetId,
    $dialogId,
    $fieldName,
))->dto();
```

##### Upload

| Request                                             | Supported |
|-----------------------------------------------------|-----------|
| Create Data Record                                  | ✅         |
| Append File(s) to a Data Record                     | ✅         |
| Upload a Single File for a Data Record              | ✅         |
| Create a Data Record & Upload File                  | ✅         |
| Create Data Record & Upload File Using Store Dialog | ✅         |
| Append a Single PDF to a Document                   | ✅         |
| Replace a PDF Document Section                      | ✅         |

> Postman splits some uploads into separate recipes; this package maps them to `CreateDataRecord` (multipart `POST …/Documents`, optional `storeDialogId`), `AppendFilesToADataRecord`, `AppendASinglePDFToADocument`, and `ReplaceAPDFDocumentSection`.

###### Create Data Record
```php
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTextDTO;

$document = $connector->send(new CreateDataRecord(
    $fileCabinetId,
    null,
    null,
    collect([
        IndexTextDTO::make('DOCUMENT_LABEL', '::data-entry::'),
    ]),
))->dto();
```

###### Create Table Data Record
```php
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDateDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDateTimeDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDecimalDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexNumericDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTableDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTextDTO;

$tableRows = collect([
    collect([
        IndexTextDTO::make('TEXT', 'project_1'),
        IndexNumericDTO::make('INT', 1),
        IndexDecimalDTO::make('DECIMAL', 1.1),
        IndexDateDTO::make('DATE', $now),
        IndexDateTimeDTO::make('DATETIME', $now),
    ]),
    collect([
        IndexTextDTO::make('TEXT', 'project_2'),
        IndexNumericDTO::make('INT', 2),
        IndexDecimalDTO::make('DECIMAL', 2.2),
        IndexDateDTO::make('DATE', $now),
        IndexDateTimeDTO::make('DATETIME', $now),
    ]),
]);


$document = $connector->send(new CreateDataRecord(
    $fileCabinetId,
    null,
    null,
    collect([
        IndexTableDTO::make('TABLE_NAME', $tableRows)
    ]),
))->dto();
```

###### Append File(s) To A Data Record
```php
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\AppendFilesToADataRecord;
use Saloon\Data\MultipartValue;

$response = $connector->send(
    new AppendFilesToADataRecord(
        fileCabinetId: $fileCabinetId,
        dataRecordId: $document->id,
        files: collect([
            new MultipartValue(
                name: 'File[]',
                value: file_get_contents(__DIR__.'/../../../../Fixtures/files/test-2.pdf'),
                filename: 'test-2.pdf',
            ),
            new MultipartValue(
                name: 'File[]',
                value: file_get_contents(__DIR__.'/../../../../Fixtures/files/test-3.pdf'),
                filename: 'test-3.pdf',
            ),
        ])
    )
)->dto();
```

###### Append A Single PDF To A Document
```php
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\AppendASinglePDFToADocument;

$response = $this->connector->send(new AppendASinglePDFToADocument(
    fileCabinetId: $fileCabinetId,
    documentId: $document->id,
    fileContent: file_get_contents(__DIR__.'/../../../../Fixtures/files/test-2.pdf'),
    fileName: 'test-2.pdf',
))->dto();
```

###### Replace A PDF Document Section
```php
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\ReplaceAPDFDocumentSection;

$response = $this->connector->send(new ReplaceAPDFDocumentSection(
    fileCabinetId: $fileCabinetId,
    sectionId: $documentWithSections->sections->first()->id,
    fileContent: file_get_contents(__DIR__.'/../../../../Fixtures/files/test-3.pdf'),
    fileName: 'test-3.pdf',
))->dto();
```

###### Batch Index Fields Update
| Request                                  | Supported |
|------------------------------------------|-----------|
| Batch Update Index Fields By Id          | ✅         |
| Batch Update Index Fields By Search      | ✅         |
| Batch Append/Update Keyword Fields By Id | ✅         |

> Use `BatchDocumentsUpdateFields` (same class covers these Postman variants).

###### Get Fields
```php
use CodebarAg\DocuWare\Requests\Fields\GetFieldsRequest;

$fields = $connector->send(new GetFieldsRequest($fileCabinetId))->dto();
```

#### Documents

##### Update Index Values
| Request                   | Supported |
|---------------------------|-----------|
| Update Index Values       | ✅         |
| Update Table Index Values | ✅         |
| Update Table Field Values | ✅         |

> Table columns use `IndexTableDTO` in the same `UpdateIndexValues` request as scalar fields (see **Update Table Data Record** below).


```php
use CodebarAg\DocuWare\Requests\Documents\UpdateIndexValues\UpdateIndexValues;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDateDTO;

$response = $connector->send(new UpdateIndexValues(
    $fileCabinetId,
    $documentId,
    collect([
        IndexTextDTO::make('DOCUMENT_LABEL', '::new-data-entry::'),
    ])
))->dto();
```

###### Update Table Data Record
```php
use CodebarAg\DocuWare\Requests\Documents\UpdateIndexValues\UpdateIndexValues;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDateDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDateTimeDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDecimalDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexNumericDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTableDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTextDTO;

$tableRows = collect([
    collect([
        IndexTextDTO::make('TEXT', 'project_1'),
        IndexNumericDTO::make('INT', 1),
        IndexDecimalDTO::make('DECIMAL', 1.1),
        IndexDateDTO::make('DATE', $now),
        IndexDateTimeDTO::make('DATETIME', $now),
    ]),
    collect([
        IndexTextDTO::make('TEXT', 'project_2'),
        IndexNumericDTO::make('INT', 2),
        IndexDecimalDTO::make('DECIMAL', 2.2),
        IndexDateDTO::make('DATE', $now),
        IndexDateTimeDTO::make('DATETIME', $now),
    ]),
]);


$document = $connector->send(new UpdateIndexValues(
    $fileCabinetId,
    null,
    null,
    collect([
        IndexTableDTO::make('TABLE_NAME', $tableRows)
    ]),
))->dto();
```

##### Modify Documents
| Request           | Supported |
|-------------------|-----------|
| Transfer Document | ✅         |
| Delete Document   | ✅         |


###### Transfer Document
```php
use CodebarAg\DocuWare\Requests\Documents\ModifyDocuments\TransferDocument;

$response = $connector->send(new TransferDocument(
    $fileCabinetId,
    $destinationFileCabinetId,
    $storeDialogId,
    $documentId,
    $fields,
))->dto();
```

###### Delete Documents
```php
use CodebarAg\DocuWare\Requests\Documents\ModifyDocuments\DeleteDocument;

$connector->send(new DeleteDocument(
    $fileCabinetId
    $documentId,
))->dto();
```

##### Clip/Unclip & Staple/Unstaple
| Request  | Supported |
|----------|-----------|
| Clip     | ✅         |
| Unclip   | ✅         |
| Staple   | ✅         |
| Unstaple | ✅         |

###### Clip
```php
use CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple\Clip;

$clip = $connector->send(new Clip(
    $fileCabinetId,
    [
        $documentId,
        $document2Id,
    ]
))->dto();
```

###### Unclip
```php
use CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple\Unclip;

$unclip = $connector->send(new Unclip(
    $fileCabinetId,
    $clipId
))->dto();
```

###### Staple
```php
use CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple\Staple;

$staple = $connector->send(new Staple(
    $fileCabinetId,
    [
        $documentId,
        $document2Id,
    ]
))->dto();
```

###### Unstaple
```php
use CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple\Unstaple;

$unclip = $connector->send(new Unstaple(
    $fileCabinetId,
    $stapleId
))->dto();
```

##### Annotations/Stamps

DocuWare's Postman collection lists several **add** operations (stamp with position / best position, text, rectangle, line, polyline). They target the same Platform route: `POST /FileCabinets/{id}/Documents/{documentId}/Annotation`, differing only by JSON (`$type`, `Annotations`, `AnnotationsPlacement`, etc.). This package exposes that as **`AddDocumentAnnotations`** with the same payload array you would send from Postman—there are no separate classes per recipe.

**Not implemented:** `DeleteAnnotation` and `UpdateTextAnnotation` are separate operations in the API (different HTTP method or path); there is no Saloon request class for them yet—contributions welcome.

| Request                    | Supported | Package class |
|----------------------------|-----------|---------------|
| Get Stamps                 | ✅         | `GetStamps` |
| Get Annotations            | ✅         | `GetDocumentAnnotations` |
| AddStampWithPosition       | ✅         | `AddDocumentAnnotations` |
| AddStampWithBestPosition   | ✅         | `AddDocumentAnnotations` |
| AddTextAnnotation          | ✅         | `AddDocumentAnnotations` |
| AddRectEntryAnnotation     | ✅         | `AddDocumentAnnotations` |
| AddLineEntryAnnotation     | ✅         | `AddDocumentAnnotations` |
| AddPolyLineEntryAnnotation | ✅         | `AddDocumentAnnotations` |
| DeleteAnnotation           | ❌         | — |
| UpdateTextAnnotation       | ❌         | — |

###### Get Stamps
```php
use CodebarAg\DocuWare\Requests\Documents\Stamps\GetStamps;

$stamps = $connector->send(new GetStamps(
    $fileCabinetId,
))->dto();
```

###### Get Annotations
```php
use CodebarAg\DocuWare\Requests\Documents\Stamps\GetDocumentAnnotations;

$annotations = $connector->send(new GetDocumentAnnotations(
    $fileCabinetId,
    $documentId,
))->dto(); // Collection<int, array<string, mixed>>
```

###### Add stamps / annotations (POST body from Postman)
```php
use CodebarAg\DocuWare\Requests\Documents\Stamps\AddDocumentAnnotations;

$result = $connector->send(new AddDocumentAnnotations(
    $fileCabinetId,
    $documentId,
    [
        // Same JSON structure as the matching Postman request (e.g. StampPlacement, TextEntry, …).
    ],
))->dto();
```

###### Documents Trash Bin
| Request           | Supported |
|-------------------|-----------|
| Get Documents     | ✅         |
| Delete Documents  | ✅         |
| Restore Documents | ✅         |


###### Get Documents
> You can use the same methods as in the search usage. The only difference is that you have to use the `trashBin` method after the `searchRequestBuilder` method.
```php


```php
use CodebarAg\DocuWare\DocuWare;

$paginatorRequest = (new DocuWare())
    ->searchRequestBuilder()
    ->trashBin()
```

###### Delete Documents
```php
use CodebarAg\DocuWare\Requests\Documents\DocumentsTrashBin\DeleteDocuments;

$delete = $connector->send(new DeleteDocuments([$documentID, $document2ID]))->dto();
```

###### Restore Documents
```php
use CodebarAg\DocuWare\Requests\Documents\DocumentsTrashBin\RestoreDocuments;

$delete = $connector->send(new RestoreDocuments([$documentID, $document2ID]))->dto();
```

##### Application Properties
| Request                       | Supported |
|-------------------------------|-----------|
| Get Application Properties    | ✅         |
| Add Application Properties    | ✅         |
| Delete Application Properties | ✅         |
| Update Application Properties | ✅         |


###### Add Application Properties
```php
use CodebarAg\DocuWare\Requests\Documents\ApplicationProperties\AddApplicationProperties;

$addProperties = $connector->send(new AddApplicationProperties(
    $fileCabinetId,
    $documentId,
    [
        [
            'Name' => 'Key1',
            'Value' => 'Key1 Value',
        ],
        [
            'Name' => 'Key2',
            'Value' => 'Key2 Value',
        ],
    ],
))->dto();
```

###### Update Application Properties
```php
use CodebarAg\DocuWare\Requests\Documents\ApplicationProperties\UpdateApplicationProperties;

$updateProperties = $connector->send(new UpdateApplicationProperties(
    $fileCabinetId,
    $documentId,
    [
        [
            'Name' => 'Key1',
            'Value' => 'Key1 Value Updated',
        ],
    ],
))->dto()->sortBy('Name');
```

###### Delete Application Properties
```php
use CodebarAg\DocuWare\Requests\Documents\ApplicationProperties\DeleteApplicationProperties;

$deleteProperties = $connector->send(new DeleteApplicationProperties(
    $fileCabinetId,
    $document->id,
    [
        'Key1',
    ],
))->dto();
```

###### Get Application Properties
```php
use CodebarAg\DocuWare\Requests\Documents\ApplicationProperties\GetApplicationProperties;

$properties = $connector->send(new GetApplicationProperties(
    $fileCabinetId,
    $document->id,
))->dto();
```

##### Sections

| Request                          | Supported |
|----------------------------------|-----------|
| Get All Sections from a Document | ✅         |
| Get a Specific Section           | ✅         |
| Delete Section                   | ✅         |
| Get Textshot                     | ✅         |

###### Get All Sections

```php
use CodebarAg\DocuWare\Requests\Documents\Sections\GetAllSectionsFromADocument;

$sections = $connector->send(new GetAllSectionsFromADocument(
    $fileCabinetId,
    $documentId
))->dto();
```

###### Get Specific Section

```php
use CodebarAg\DocuWare\Requests\Documents\Sections\GetASpecificSection;

$section = $connector->send(new GetASpecificSection(
    $fileCabinetId,
    $sectionsId
))->dto();
```

###### Delete Section

```php
use CodebarAg\DocuWare\Requests\Documents\Sections\DeleteSection;

$deleted = $connector->send(new DeleteSection(
    $fileCabinetId,
    $sectionId
))->dto();
```

###### Get Textshot

```php
use CodebarAg\DocuWare\Requests\Documents\Sections\GetTextshot;

$deleted = $connector->send(new GetTextshot(
    $fileCabinetId,
    $sectionId
))->dto();
```

##### Download
| Request            | Supported |
|--------------------|-----------|
| Download Document  | ✅         |
| Download Section   | ✅         |
| Download Thumbnail | ✅         |


###### Download Document
```php
use CodebarAg\DocuWare\Requests\Documents\Download\DownloadDocument;

$contents = $connector->send(new DownloadDocument(
    $fileCabinetId,
    $documentId
))->dto();
```

###### Download Section
```php
use CodebarAg\DocuWare\Requests\Documents\Download\DownloadSection;

$contents = $connector->send(new DownloadSection(
    $fileCabinetId,
    $sectionId
))->dto();
```

###### Download Thumbnail
```php
use CodebarAg\DocuWare\Requests\Documents\Download\DownloadThumbnail;

$contents = $connector->send(new DownloadThumbnail(
    $fileCabinetId,
    $sectionId
))->dto();
```

#### Workflow

##### Workflow History
| Request                             | Supported |
|-------------------------------------|-----------|
| Get Document Workflow History       | ✅         |
| Get Document Workflow History Steps | ✅         |

###### Get Document Workflow History
```php
use CodebarAg\DocuWare\Requests\Workflow\GetDocumentWorkflowHistory;

$history = $this->connector->send(new GetDocumentWorkflowHistory(
    $fileCabinetId,
    $documentId
))->dto();
```

###### Get Document Workflow History Steps
```php
use CodebarAg\DocuWare\Requests\Workflow\GetDocumentWorkflowHistorySteps;

$historySteps = $this->connector->send(new GetDocumentWorkflowHistorySteps(
    $workflowId,
    $historyId,
))->dto();
```































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

```php
use CodebarAg\DocuWare\Facades\DocuWare;
```

### Make encrypted URL for a document in a file cabinet.
```php 
$fileCabinetId = '87356f8d-e50c-450b-909c-4eaccd318fbf';
$documentId = 42;

$url = DocuWare::url()
    ->fileCabinet($fileCabinetId)
    ->document($documentId)
    ->make();
```

### Make encrypted URL for a document in a basket.
```php 
$basketId = 'b_87356f8d-e50c-450b-909c-4eaccd318fbf';

$url = DocuWare::url()
    ->basket($basketId)
    ->document($documentId)
    ->make();
```

### Make encrypted URL valid for a specific amount of time.
>  In the example below the URL is valid for one week, afterward the URL is no longer working.
```php
$url = DocuWare::url()
    ->fileCabinet($fileCabinetId)
    ->document($documentId)
    ->validUntil(now()->addWeek())
    ->make();
```


## 🏋️ Document Index Fields DTO showcase

```php
CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTextDTO {
  +name: "FIELD_TEXT"                               // string
  +value: "Value"                                   // null|string
}
```

```php
CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexNumericDTO {
  +name: "FIELD_NUMERIC"                            // string
  +value: 1                                         // null|int
}
```

```php
CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDecimalDTO {
  +name: "FIELD_DECIMAL"                            // string
  +value: 1.00                                      // null|int|float
}
```

```php
CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDateDTO {
  +name: "FIELD_DATE"                               // string
  +value: now(),                                    // null|Carbon
}
```

```php
CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDateTimeDTO {
  +name: "FIELD_DATETIME"                           // string
  +value: now(),                                    // null|Carbon
}
```

```php
CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexKeywordDTO {
  +name: "FIELD_KEYWORD"                            // string
  +value: "Value"                                   // null|string
}
```

```php
CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexMemoDTO {
  +name: "FIELD_MEMO"                               // string
  +value: "Value"                                   // null|string
}
```

```php
CodebarAg\DocuWare\DTO\OrganizationIndex {
  +id: "2f071481-095d-4363-abd9-29ef845a8b05"              // string
  +name: "Fake File Cabinet"                               // string
  +guid: "1334c006-f095-4ae7-892b-fe59282c8bed"            // string|null
}
```

```php
CodebarAg\DocuWare\DTO\Organization {
  +id: "2f071481-095d-4363-abd9-29ef845a8b05"              // string
  +name: "Fake File Cabinet"                               // string
  +guid: "1334c006-f095-4ae7-892b-fe59282c8bed"            // string|null
  +additionalInfo: []                                      // array
  +configurationRights: []                                 // array
}
```

```php
CodebarAg\DocuWare\DTO\FileCabinet {
  +id: "2f071481-095d-4363-abd9-29ef845a8b05"              // string
  +name: "Fake File Cabinet"                               // string
  +color: "Yellow"                                         // string
  +isBasket: true                                          // bool
  +assignedCabinet: "889c13cc-c636-4759-a704-1e6500d2d70f" // string
}
```

```php
CodebarAg\DocuWare\DTO\Dialog {
  +id: "fae3b667-53e9-48dd-9004-34647a26112e"            // string
  +type: "ResultList"                                    // string
  +label: "Fake Dialog"                                  // string
  +isDefault: true                                       // boolean
  +fileCabinetId: "1334c006-f095-4ae7-892b-fe59282c8bed" // string
}
```

```php
CodebarAg\DocuWare\DTO\Field {
  +name: "FAKE_FIELD"  // string
  +label: "Fake Field" // string
  +type: "Memo"        // string
  +scope: "User"       // string
```

```php
CodebarAg\DocuWare\DTO\Field {
  +name: "FAKE_FIELD"  // string
  +label: "Fake Field" // string
  +type: "Memo"        // string
  +scope: "User"       // string
```

```php
CodebarAg\DocuWare\DTO\Document {
  +id: 659732                                              // integer
  +file_size: 765336                                       // integer
  +total_pages: 100                                        // integer
  +title: "Fake Title"                                     // string
  +extension: ".pdf"                                       // string
  +content_type: "application/pdf"                         // string
  +file_cabinet_id: "a233b03d-dc63-42dd-b774-25b3ff77548f" // string
  +created_at: Illuminate\Support\Carbon                   // Carbon
  +updated_at: Illuminate\Support\Carbon                   // Carbon
  +fields: Illuminate\Support\Collection {                 // Collection|DocumentField[]
    #items: array:2 [
      0 => CodebarAg\DocuWare\DTO\DocumentField            // DocumentField
      1 => CodebarAg\DocuWare\DTO\DocumentField            // DocumentField
    ]
  }
}
```

```php
CodebarAg\DocuWare\DTO\Section {#23784▶
  +id: "5589-5525"
  +contentType: "text/plain"
  +haveMorePages: true
  +pageCount: 1
  +fileSize: 32
  +originalFileName: "example.txt"
  +contentModified: "/Date(1702395557000)/"
  +annotationsPreview: false
  +hasTextAnnotations: null
}
```

```php
CodebarAg\DocuWare\DTO\DocumentThumbnail {
  +mime: "image/png"                                        // string
  +data: "somedata"                                         // string
  +base64: "data:image/png;base64,WXpJNWRGcFhVbWhrUjBVOQ==" // string
}
```

```php
CodebarAg\DocuWare\DTO\TableRow {
   +fields: Illuminate\Support\Collection {                 // Collection|DocumentField[]
    #items: array:2 [
      0 => CodebarAg\DocuWare\DTO\DocumentField            // DocumentField
      1 => CodebarAg\DocuWare\DTO\DocumentField            // DocumentField
    ]
}
```

```php
CodebarAg\DocuWare\DTO\DocumentPaginator
  +total: 39                                  // integer
  +per_page: 10                               // integer
  +current_page: 9                            // integer
  +last_page: 15                              // integer
  +from: 1                                    // integer
  +to: 10                                     // integer
  +documents: Illuminate\Support\Collection { // Collection|Document[]
    #items: array:2 [
      0 => CodebarAg\DocuWare\DTO\Document    // Document
      1 => CodebarAg\DocuWare\DTO\Document    // Document
    ]
  }
  +error: CodebarAg\DocuWare\DTO\ErrorBag {   // ErrorBag|null
    +code: 422                                // int
    +message: "'000' is not valid cabinet id" // string
  }
}
```


## 📦 Caching requests

> All Get Requests are cachable and will be cached by default. To determine if the response is cached you can use the following method:

### Is Cached
```php 
$connector = new DocuWareConnector();

$response = $connector->send(new GetDocumentRequest($fileCabinetId, $documentId));
$response->isCached(); // false

// Next time the request is sent

$response = $connector->send(new GetDocumentRequest($fileCabinetId, $documentId));
$response->isCached(); // true
```


### Invalidate Cache
> To invalidate the cache for a specific request you can use the following method:
```php 
$connector = new DocuWareConnector();

$request = new GetDocumentRequest($fileCabinetId, $documentId);
$request->invalidateCache();

$response = $connector->send($request);
```

### Disable Caching
> To temporarily disable caching for a specific request you can use the following method:
```php 
$connector = new DocuWareConnector();

$request = new GetDocumentRequest($fileCabinetId, $documentId);
$request->disableCaching();

$response = $connector->send($request);
```


## 💥 Exceptions explained

- `CodebarAg\DocuWare\Exceptions\UnableToMakeRequest`

This is thrown if you are not authorized to make the request.

---

- `CodebarAg\DocuWare\Exceptions\UnableToProcessRequest`

This is thrown if you passed wrong attributes. For example a file cabinet ID
which does not exist.

---

- `CodebarAg\DocuWare\Exceptions\UnableToLogin`

This exception can only be thrown during the login if the credentials did not
match.

---

- `CodebarAg\DocuWare\Exceptions\UnableToLoginNoCookies`

This exception can only be thrown during the login if there was no cookies in
the response from the api.

---

- `CodebarAg\DocuWare\Exceptions\UnableToFindPassphrase`

This exception can only be thrown during the url making if the passphrase
could not be found.

---

- `CodebarAg\DocuWare\Exceptions\UnableToMakeUrl`

Something is wrong during the URL making.

---

- `CodebarAg\DocuWare\Exceptions\UnableToUpdateFields`

No fields were supplied.

---

- `CodebarAg\DocuWare\Exceptions\UnableToGetDocumentCount`

Something is wrong with the response from getting the document count.

---

- `Illuminate\Http\Client\RequestException`

All other cases if the response is not successfully.


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
    | In Laravel 12+, CACHE_STORE is used instead of CACHE_DRIVER.
    |
    */

    'cache_driver' => env('DOCUWARE_CACHE_DRIVER', env('CACHE_STORE', 'file')),

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
    | Platform path
    |--------------------------------------------------------------------------
    |
    | Matches Postman {{Platform}} (default DocuWare/Platform).
    |
    */

    'platform_path' => env('DOCUWARE_PLATFORM_PATH', 'DocuWare/Platform'),

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
            'driver' => env('DOCUWARE_CACHE_DRIVER', env('CACHE_STORE', 'file')),
            'lifetime_in_seconds' => env('DOCUWARE_CACHE_LIFETIME_IN_SECONDS', 60),
        ],
        'request' => [
            'timeout_in_seconds' => env('DOCUWARE_TIMEOUT', 60),
        ],

        'client_id' => env('DOCUWARE_CLIENT_ID', 'docuware.platform.net.client'),
        'scope' => env('DOCUWARE_SCOPE', 'docuware.platform'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tests
    |--------------------------------------------------------------------------
    |
    */
    'tests' => [
        'file_cabinet_id' => env('DOCUWARE_TESTS_FILE_CABINET_ID'),
        'dialog_id' => env('DOCUWARE_TESTS_DIALOG_ID'),
        'basket_id' => env('DOCUWARE_TESTS_BASKET_ID'),
        'org_id' => env('DOCUWARE_TESTS_ORG_ID', env('DOCUWARE_TESTS_ORGANIZATION_ID')),
        'search_dialog_id' => env('DOCUWARE_TESTS_SEARCH_DIALOG_ID'),
        'store_dialog_id' => env('DOCUWARE_TESTS_STORE_DIALOG_ID'),
        'document_id' => env('DOCUWARE_TESTS_DOCUMENT_ID'),
    ],
];
```

## Postman collection parity & Saloon fixtures

The official DocuWare Postman collection uses `{{ServerUrl}}` and `{{Platform}}` (default `DocuWare/Platform`). This package maps them to Laravel env vars — see [`.env.example`](.env.example) for a **Postman variable → `DOCUWARE_*`** table.

- **Parity matrix** (endpoints vs request classes): [`docs/postman-parity.md`](docs/postman-parity.md).
- **Platform path**: `DOCUWARE_PLATFORM_PATH` (used by `DocuWareConnector`, `GetResponsibleIdentityService`, and encrypted Web Client URLs in `DocuWareUrl`).
- **CI-friendly tests**: Default `composer test` runs **unit**, **DTO**, and **Saloon fixture** tests (`tests/Feature/SaloonFixtures`). HTTP responses are replayed from JSON files under [`tests/Fixtures/saloon/`](tests/Fixtures/saloon) using [Saloon fixtures](https://docs.saloon.dev/digging-deeper/testing) (`MockClient` + `Fixture`). Bodies use Saloon’s recorded format: `statusCode`, `headers`, `data` (raw response body), `context`.
- **Recording fixtures** (optional): Edit [`tests/Manual/RecordGetOrganizationFixtureTest.php`](tests/Manual/RecordGetOrganizationFixtureTest.php), remove the `->skip(...)`, set real `DOCUWARE_*` credentials, then run:
  ```bash
  composer test:manual
  ```
  (`composer test:manual` runs only `tests/Manual`.)
  The recorder **only writes** `tests/Fixtures/saloon/get-organization.json` when the HTTP response is successful and looks like JSON, so a bad run (HTML error page, 401, etc.) **does not overwrite** a good committed fixture—the test fails instead.
  Review the generated JSON for secrets, commit if safe, then restore the skip.
- **Live tenant tests** (destructive cleanup, real API): `composer test:live` runs the `integration` PHPUnit testsuite (`tests/Integration`). Requires valid DocuWare credentials and test cabinet IDs in `phpunit.xml` or the environment. **Do not** run integration against DocuWare with `pest --parallel` or multiple concurrent `test:live` processes (rate limits and shared cabinet cleanup). Use a **single** sequential run.

## 🚧 Testing

Copy your own phpunit.xml-file.

```bash
cp phpunit.xml.dist phpunit.xml
```

Modify environment variables in the phpunit.xml-file:

```xml
<env name="DOCUWARE_URL" value="https://domain.docuware.cloud"/>
<env name="DOCUWARE_PLATFORM_PATH" value="DocuWare/Platform"/>
<env name="DOCUWARE_USERNAME" value="user@domain.test"/>
<env name="DOCUWARE_PASSWORD" value="password"/>
<env name="DOCUWARE_PASSPHRASE" value="passphrase"/>
<env name="DOCUWARE_TIMEOUT" value="30"/>
<env name="DOCUWARE_CACHE_DRIVER" value="file"/>
<env name="DOCUWARE_CACHE_LIFETIME_IN_SECONDS" value="0"/>
<env name="DOCUWARE_CLIENT_ID" value="docuware.platform.net.client"/>
<env name="DOCUWARE_SCOPE" value="docuware.platform"/>

<env name="DOCUWARE_TESTS_FILE_CABINET_ID" value=""/>
<env name="DOCUWARE_TESTS_DIALOG_ID" value=""/>
<env name="DOCUWARE_TESTS_BASKET_ID" value=""/>
<env name="DOCUWARE_TESTS_ORG_ID" value=""/>
<env name="DOCUWARE_TESTS_SEARCH_DIALOG_ID" value=""/>
<env name="DOCUWARE_TESTS_STORE_DIALOG_ID" value=""/>
<env name="DOCUWARE_TESTS_DOCUMENT_ID" value=""/>
```

Default test run (no live DocuWare required):

```bash
composer test
```

Against a real system (integration suite):

```bash
composer test:live
```

Run integration **one process at a time** (no `pest --parallel`). Keep real credentials in a **local** `phpunit.xml` (this file is gitignored in this repo); use GitHub Actions secrets in CI.

In GitHub Actions ([`.github/workflows/run-tests.yml`](.github/workflows/run-tests.yml)), every matrix job runs `composer test` first; `composer test:live` runs **only** on PHP 8.3 with `prefer-stable`, and **only** when `DOCUWARE_URL`, `DOCUWARE_USERNAME`, and `DOCUWARE_PASSWORD` are set as repository secrets (optional `DOCUWARE_PASSPHRASE` and `DOCUWARE_TESTS_*` IDs as needed).

## 📝 Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## ✏️ Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## 🧑‍💻 Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## 🙏 Credits

- [Sebastian Bürgin-Fix](https://github.com/StanBarrows)
- [All Contributors](../../contributors)
- [Skeleton Repository from Spatie](https://github.com/spatie/package-skeleton-laravel)
- [Laravel Package Training from Spatie](https://spatie.be/videos/laravel-package-training)

## 🎭 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

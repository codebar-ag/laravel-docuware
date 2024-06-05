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

## 💡 What is DocuWare?

DocuWare provides cloud document management and workflow automation software
that enables you to digitize, secure and work with business documents,
then optimize the processes that power the core of your business.

## 🛠 Requirements

<details>
 <summary>Version Support</summary>

### \> = v11.0 (alpha)

- PHP: `^8.2``
  - Laravel: `^11.*`
  - DocuWare Cloud Access

### \> = v4.0 (alpha)

- PHP: `^8.2``
- Laravel: `^11.*`
- DocuWare Cloud Access

### \> = v3.0

- PHP: `^8.2``
- Laravel: `^10.*`
- DocuWare Cloud Access

### \> = v2.0

- PHP: `^8.1` |`^8.2`
- Laravel: `^9.*` | `^10.*`
- DocuWare Cloud Access

### \> = v1.2

- PHP: `^8.1`
- Laravel: `^9.*`
- DocuWare Cloud Access

### \< v1.2

- PHP: `^8.0`
- Laravel: `^8.*`
- DocuWare Cloud Access

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
| FileCabinets/Upload                 | Append File(s) to a Data Record                             | ❌         | -    |
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
| Documents/ModifyDocuments           | Transfer Document                                           | ✅         | -    |
| Documents/ModifyDocuments           | Delete Document                                             | ✅         |      |
| Documents/ClipUnclip&StapleUnstaple | Clip                                                        | ✅         |      |
| Documents/ClipUnclip&StapleUnstaple | Unclip                                                      | ✅         |      |
| Documents/ClipUnclip&StapleUnstaple | Staple                                                      | ✅         |      |
| Documents/ClipUnclip&StapleUnstaple | Unstaple                                                    | ✅         |      |
| Documents/AnnotationsStamps         | AddStampWithPosition                                        | ❌         | -    |
| Documents/AnnotationsStamps         | AddStampWithBestPosition                                    | ❌         | -    |
| Documents/AnnotationsStamps         | AddTextAnnotation                                           | ❌         | -    |
| Documents/AnnotationsStamps         | AddRectEntryAnnotation                                      | ❌         | -    |
| Documents/AnnotationsStamps         | AddLineEntryAnnotation                                      | ❌         | -    |
| Documents/AnnotationsStamps         | AddPolyLineEntryAnnotation                                  | ❌         | -    |
| Documents/AnnotationsStamps         | DeleteAnnotation                                            | ❌         | -    |
| Documents/AnnotationsStamps         | UpdateTextAnnotation                                        | ❌         | -    |
| Documents/AnnotationsStamps         | Get Stamps                                                  | ❌         | -    |
| Documents/DocumentsTrashBin         | Get Documents                                               | ✅         |      |
| Documents/DocumentsTrashBin         | Delete Documents                                            | ✅         |      |
| Documents/DocumentsTrashBin         | Restore Documents                                           | ✅         |      |
| Documents/ApplicationProperties     | Get Application Properties                                  | ✅         | -    |
| Documents/ApplicationProperties     | Add Application Properties                                  | ✅         | -    |
| Documents/ApplicationProperties     | Delete Application Properties                               | ✅         | -    |
| Documents/ApplicationProperties     | Update Application Properties                               | ✅         | -    |
| Documents/Sections                  | Get All Sections from a Document                            | ✅         | -    |
| Documents/Sections                  | Get a Specific Section                                      | ✅         | -    |
| Documents/Sections                  | Delete Section                                              | ✅         | -    |
| Documents/Download                  | Download Document                                           | ✅         |      |
| Documents/Download                  | Download Section                                            | ✅         | -    |
| Documents/Download                  | Download Thumbnail                                          | ✅         |      |
| Workflow                            | Get Document Workflow History                               | ✅         | -    |
| Workflow                            | Get Document Workflow History Steps                         | ✅         | -    |

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
<details>
 <summary>Getting Started with OAuth</summary>

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

### Extending the connector

We understand it may be repetitive to pass the configuration every time you create a new connector. 

You can extend the connector and set the configuration once.

#### Create a new connector

```php
<?php

namespace App\Connectors;

use CodebarAg\DocuWare\Connectors\DocuWareConnector;
use CodebarAg\DocuWare\DTO\Config\ConfigWithCredentials;

class CustomDocuWareConnector extends DocuWareConnector
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
use App\Connectors\CustomDocuWareConnector;
use CodebarAg\DocuWare\DTO\Config\ConfigWithCredentials;

$connector = new CustomDocuWareConnector();
```


</details>

### Available Requests

<details>
 <summary>Available Requests</summary>

```php
/**
 * Return an organization.
 */
 
$organization = $connector->send(new GetOrganization($id))->dto();
```

```php
/**
 * Return all file cabinets.
 */
 
$fileCabinets = $connector->send(new GetAllFileCabinetsAndDocumentTrays())->dto();
```

```php
/**
 * Return all fields of a file cabinet.
 */
 
$fields = $connector->send(new GetFieldsRequest($fileCabinetId))->dto();
```

```php
/**
 * Return all dialogs of a file cabinet.
 */
 
$dialogs = $connector->send(new GetAllDialogs($fileCabinetId))->dto();
```

```php
/**
 * Return all used values for a specific field.
 */
 
$values = $connector->send(new GetSelectList($fileCabinetId, $dialogId, $fieldName))->dto();
```

```php
/**
 * Return a document.
 */
 
$document = $connector->send(new GetASpecificDocumentFromAFileCabinet($fileCabinetId, $documentId))->dto();
```

```php
/**
 * Return all documents for a file cabinet.
 */
 
$documents = $connector->send(new GetDocumentsFromAFileCabinet($fileCabinetId))->dto();
```

```php
/**
 * Return image preview of a document.
 */
 
$content = $connector->send(new GetDocumentPreviewRequest($fileCabinetId, $documentId))->dto();
```

```php
/**
 * Download single document.
 */
 
$content = $connector->send(new DownloadDocument($fileCabinetId, $documentId))->dto();
```

```php
/**
 * Get sections of a document.
 */
 
$section = $connector->send(new GetSectionsRequest($fileCabinetId, $documentId))->dto();
```

```php
/**
 * Download a document thumbnail.
 * 
 * You will use $section->id from above as $thumbnailId.
 */
 
$thumbnail = $connector->send(new DownloadThumbnail($fileCabinetId, $thumbnailId, $page = 0))->dto();
```

```php
/**
 * Update value of a indexed field.
 */
 
$value = $connector->send(new UpdateIndexValues($fileCabinetId, $documentId, [$fieldName => $newValue]))->dto();
```

```php
/**
 * Update multiple values of indexed fields.
 */
 
$values = $connector->send(new UpdateIndexValues($fileCabinetId, $documentId, [
    $fieldName => $newValue,
    $field2Name => $new2Value,
]))->dto();
```

```php
/**
 * Upload new document.
 */
 
$document = $connector->send(new CreateDataRecord($fileCabinetId, $fileContent, $fileName))->dto();
```

```php
/**
 * Get total document count.
 */
 
$content = $connector->send(new GetTotalNumberOfDocuments($fileCabinetId, $dialogId))->dto();
```

```php
/**
 * Upload new document with index values.
 */
 
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\PrepareDTO;
 
$indexes = collect([
    PrepareDTO::make('FIELD_TEXT', 'Indexed Text'),
    PrepareDTO::make('FIELD_NUMERIC', 1),
    PrepareDTO::make('FIELD_DECIMAL', 1.00),
    PrepareDTO::make('FIELD_DATE', now()),
]);

$document = $connector->send(new PostDocumentRequest(
    $fileCabinetId,
    $fileContent,
    $fileName,
    $indexes,
))->dto();
```

```php
/**
 * Upload new data entry with index values.
 */
 
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\PrepareDTO;
 
$indexes = collect([
    PrepareDTO::make('FIELD_TEXT', 'Indexed Text'),
    PrepareDTO::make('FIELD_NUMERIC', 1),
    PrepareDTO::make('FIELD_DECIMAL', 1.00),
    PrepareDTO::make('FIELD_DATE', now()),
]);

$document = $connector->send(new PostDocumentRequest(
    $fileCabinetId,
    null,
    null,
    $indexes,
))->dto();
```

```php
/**
 * Delete document.
 */
 
$connector->send(new DeleteDocumentRequest($fileCabinetId, $document->id))->dto();
```

</details>

## 🔍 Search usage
<details>
 <summary>Search Usage</summary>

```php
use CodebarAg\DocuWare\Facades\DocuWare;
use CodebarAg\DocuWare\Connectors\DocuWareConnector;

$connector = new DocuWareConnector();
```

```php
/**
 * Most basic example to search for documents. You only need to provide a valid
 * file cabinet id.
 */
 
$fileCabinetId = '87356f8d-e50c-450b-909c-4eaccd318fbf';

$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($fileCabinetId)
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

```php
/**
 * Search in multiple file cabinets. Provide an array of file cabinet ids.
 */
 
$fileCabinetIds = [
    '0ee72de3-4258-4353-8020-6a3ff6dd650f',
    '3f9cb4ff-82f2-44dc-b439-dd648269064f',
];

$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinets($fileCabinetIds)
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

```php
/**
 * Find results on the next page. 
 * 
 * Default: 1
 */
 
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->page(2)
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

```php
/**
 * Define the number of results which should be shown per page.
 * 
 * Default: 50
 */
 
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->perPage(30)
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

```php
/**
 * Use the full-text search. You have to activate full-text search in your file
 * cabinet before you can use this feature.
 */
 
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->fulltext('My secret document')
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

```php
/**
 * Search documents which are created from the first of march.
 */
 
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->filterDate('DWSTOREDATETIME', '>=', Carbon::create(2021, 3, 1))
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

```php
/**
 * Search documents which are created until the first of april.
 */
 
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->filterDate('DWSTOREDATETIME', '<', Carbon::create(2021, 4, 1))
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

```php
/**
 * Order the results by field name. Supported values: 'asc', 'desc'
 */
 
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->orderBy('DWSTOREDATETIME', 'desc')
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

```php
/**
 * Search documents filtered to the value. You can specify multiple filters.
 */
 
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->filter('TYPE', 'Order')
    ->filter('OTHER_FIELD', 'other')
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

```php
/**
 * Search documents filtered to multiple values.
 */
 
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->filterIn('TYPE', ['Order', 'Invoice'])
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

```php
/**
 * You can specify the dialog which should be used.
 */
 
$dialogId = 'bb42c30a-89fc-4b81-9091-d7e326caba62';

$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->dialog($dialogId)
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

```php  
/**
 * You can also combine everything.
 */
 
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
</details>

## 🖼 Make encrypted URL

<details>
 <summary>Make encrypted URL</summary>

```php
use CodebarAg\DocuWare\Facades\DocuWare;
```

```php
/**
 * Make encrypted URL for a document in a file cabinet.
 */
 
$fileCabinetId = '87356f8d-e50c-450b-909c-4eaccd318fbf';
$documentId = 42;

$url = DocuWare::url()
    ->fileCabinet($fileCabinetId)
    ->document($documentId)
    ->make();
```

```php
/**
 * Make encrypted URL for a document in a basket.
 */
 
$basketId = 'b_87356f8d-e50c-450b-909c-4eaccd318fbf';

$url = DocuWare::url()
    ->basket($basketId)
    ->document($documentId)
    ->make();
```

```php
/**
 * Make encrypted URL valid for a specific amount of time. In the example below
 * the URL is valid for one week. Afterwards the URL is no longer working.
 */
 
$url = DocuWare::url()
    ->fileCabinet($fileCabinetId)
    ->document($documentId)
    ->validUntil(now()->addWeek())
    ->make();
```

</details>

## 🏋️ Document Index Fields DTO showcase

<details>
 <summary>Document Index Fields DTO showcase</summary>

```php
CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTextDTO {
  +name: "FIELD_TEXT"                               // string
  +value: "Value"                                   // null|string
}
```

```php
CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexNumericDTO {
  +name: "FIELD_NUMERIC"                                 // string
  +value: 1                                             // null|int
}
```

```php
CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDecimalDTO {
  +name: "FIELD_DECIMAL"                                  // string
  +value: 1.00                                           // null|int|float
}
```

```php
 {
  +name: "FIELD_DATE"                                      // string
  +value: now(),                                           // null|Carbon
}
```

```php
 {
  +name: "FIELD_DATETIME"                                     // string
  +value: now(),                                             // null|Carbon
}
```

```php
 {
  +name: "FIELD_TABLE"                                        // string
  +value: collect([
      0 => [
         [
            'NAME' => 'TABLE_ID',
            'VALUE' => '1',
         ],
         [
            'NAME' => 'TABLE_DATE',
            'VALUE' => Carbon::class 
         ],
         [
            'NAME' => 'TABLE_DECIMALE',
            'VALUE' => 1.00,
         ],
      ]
])                                                         // null|Collection|array
}
```

</details>

## 🏋️ DTO Showcase
<details>
 <summary>DTO Showcase</summary>

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

</details>


## 📦 Caching requests

<details>
 <summary>Caching requests</summary>

All Get Requests are cachable and will be cached by default.

To determine if the response is cached you can use the following method:

```php 
$connector = new DocuWareConnector();

$response = $connector->send(new GetDocumentRequest($fileCabinetId, $documentId));
$response->isCached(); // false

// Next time the request is sent

$response = $connector->send(new GetDocumentRequest($fileCabinetId, $documentId));
$response->isCached(); // true
```

To invalidate the cache for a specific request you can use the following method:

```php 
$connector = new DocuWareConnector();

$request = new GetDocumentRequest($fileCabinetId, $documentId);
$request->invalidateCache();

$response = $connector->send($request);
```

To temporarily disable caching for a specific request you can use the following method:

```php 
$connector = new DocuWareConnector();

$request = new GetDocumentRequest($fileCabinetId, $documentId);
$request->disableCaching();

$response = $connector->send($request);
```

</details>

## 💥 Exceptions explained

<details>
 <summary>Exceptions explained</summary>

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

Following events will be fired:

```php 
use CodebarAg\DocuWare\Events\DocuWareResponseLog;

// Log each response from the DocuWare REST API.
DocuWareResponseLog::class => [
    //
],
```

</details>

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
        'section' => (int) env('DOCUWARE_TESTS_SECTION'),
        'organization_id' => env('DOCUWARE_TESTS_ORGANIZATION_ID'),
        'document_id' => (int) env('DOCUWARE_TESTS_DOCUMENT_ID'),
        'document_file_size_preview' => (int) env('DOCUWARE_TESTS_DOCUMENT_FILE_SIZE_PREVIEW'),
        'document_file_size' => (int) env('DOCUWARE_TESTS_DOCUMENT_FILE_SIZE'),
        'document_count' => (int) env('DOCUWARE_TESTS_DOCUMENT_COUNT'),
        'document_thumbnail_mime_type' => env('DOCUWARE_TESTS_DOCUMENT_THUMBNAIL_MIME_TYPE'),
        'document_thumbnail_file_size' => (int) env('DOCUWARE_TESTS_DOCUMENT_THUMBNAIL_FILE_SIZE'),
        'document_ids' => json_decode(env('DOCUWARE_TESTS_DOCUMENTS_IDS', '[]')),
        'documents_file_size' => (int) env('DOCUWARE_TESTS_DOCUMENTS_FILE_SIZE'),
        'field_name' => env('DOCUWARE_TESTS_FIELD_NAME'),
        'field_name_2' => env('DOCUWARE_TESTS_FIELD_NAME_2'),
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

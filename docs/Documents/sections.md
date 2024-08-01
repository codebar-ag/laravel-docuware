# Sections
| Request                          | Supported |
|----------------------------------|-----------|
| Get All Sections from a Document | ✅         |
| Get a Specific Section           | ✅         |
| Delete Section                   | ✅         |


### Get All Sections
```php
use CodebarAg\DocuWare\Requests\Documents\Sections\GetAllSectionsFromADocument;

$sections = $connector->send(new GetAllSectionsFromADocument(
    $fileCabinetId,
    $documentId
))->dto();
```

### Get Specific Section
```php
use CodebarAg\DocuWare\Requests\Documents\Sections\GetASpecificSection;

$section = $connector->send(new GetASpecificSection(
    $fileCabinetId,
    $sectionsId
))->dto();
```

### Delete Section
```php
use CodebarAg\DocuWare\Requests\Documents\Sections\DeleteSection;

$deleted = $connector->send(new DeleteSection(
    $fileCabinetId,
    $sectionId
))->dto();
```

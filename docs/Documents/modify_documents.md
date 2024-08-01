# Modify Documents
| Request           | Supported |
|-------------------|-----------|
| Transfer Document | ✅         |
| Delete Document   | ✅         |


### Transfer Document
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

### Delete Documents
```php
use CodebarAg\DocuWare\Requests\Documents\ModifyDocuments\DeleteDocument;

$connector->send(new DeleteDocument(
    $fileCabinetId
    $documentId,
))->dto();
```

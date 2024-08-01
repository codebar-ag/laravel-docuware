# Documents Trash Bin
| Request           | Supported |
|-------------------|-----------|
| Get Documents     | ✅         |
| Delete Documents  | ✅         |
| Restore Documents | ✅         |


### Get Documents
> You can use the same methods as in the search usage. The only difference is that you have to use the `trashBin` method after the `searchRequestBuilder` method.
```php


```php
use CodebarAg\DocuWare\DocuWare;

$paginatorRequest = (new DocuWare())
    ->searchRequestBuilder()
    ->trashBin()
```

#### Delete Documents
```php
use CodebarAg\DocuWare\Requests\Documents\DocumentsTrashBin\DeleteDocuments;

$delete = $connector->send(new DeleteDocuments([$documentID, $document2ID]))->dto();
```

#### Restore Documents
```php
use CodebarAg\DocuWare\Requests\Documents\DocumentsTrashBin\RestoreDocuments;

$delete = $connector->send(new RestoreDocuments([$documentID, $document2ID]))->dto();
```

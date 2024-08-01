# Application Properties
| Request                       | Supported |
|-------------------------------|-----------|
| Get Application Properties    | ✅         |
| Add Application Properties    | ✅         |
| Delete Application Properties | ✅         |
| Update Application Properties | ✅         |


### Add Application Properties
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

### Update Application Properties
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

### Delete Application Properties
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

### Get Application Properties
```php
use CodebarAg\DocuWare\Requests\Documents\ApplicationProperties\GetApplicationProperties;

$properties = $connector->send(new GetApplicationProperties(
    $fileCabinetId,
    $document->id,
))->dto();
```

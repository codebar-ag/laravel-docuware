# Dialogs

| Request                        | Supported |
|--------------------------------|-----------|
| Get All Dialogs                | ✅         |
| Get a Specific Dialog          | ✅         |
| Get Dialogs of a Specific Type | ✅         |

### Get All Dialogs
```php
use CodebarAg\DocuWare\Requests\FileCabinets\Dialogs\GetAllDialogs;

$dialogs = $connector->send(new GetAllDialogs($fileCabinetId))->dto();
```

### Get Dialogs of a Specific Type
```php
use CodebarAg\DocuWare\Requests\FileCabinets\Dialogs\GetASpecificDialog;

$dialog = $connector->send(new GetASpecificDialog($fileCabinetId, $dialogId))->dto();
```

### Get Dialogs Of A Specific Type
```php
use CodebarAg\DocuWare\Enums\DialogType;
use CodebarAg\DocuWare\Requests\FileCabinets\Dialogs\GetDialogsOfASpecificType;

$dialogs = $connector->send(new GetDialogsOfASpecificType($fileCabinetId, DialogType::SEARCH))->dto();
```


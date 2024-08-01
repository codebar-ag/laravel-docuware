# General
| Request                                      | Supported |
|----------------------------------------------|-----------|
| Get Select Lists & Get Filtered Select Lists | ✅         |

### Get Select Lists
```php
use CodebarAg\DocuWare\Requests\FileCabinets\SelectLists\GetSelectLists;

$types = $this->connector->send(new GetSelectLists(
    $fileCabinetId,
    $dialogId,
    $fieldName,
))->dto();
```

# General
| Request                                  | Supported |
|------------------------------------------|-----------|
| Batch Update Index Fields By Id          | ❌         |
| Batch Update Index Fields By Search      | ❌         |
| Batch Append/Update Keyword Fields By Id | ❌         |

> Not Currently Supported

#### Get Fields
```php
use CodebarAg\DocuWare\Requests\Fields\GetFieldsRequest;

$fields = $connector->send(new GetFieldsRequest($fileCabinetId))->dto();
```

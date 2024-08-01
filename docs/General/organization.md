# Organization

| Request                                                     | Supported |
|-------------------------------------------------------------|-----------|
| Get Login Token                                             | ✅         |
| Get Organization                                            | ✅         |
| Get All File Cabinets and Document Trays                    | ✅         |


### Get Organization
```php
use CodebarAg\DocuWare\Requests\General\Organization\GetOrganization;

$organizations = $this->connector->send(new GetOrganization())->dto();
```

### Get All File Cabinets And Document Trays
```php
use CodebarAg\DocuWare\Requests\General\Organization\GetAllFileCabinetsAndDocumentTrays;

$cabinetsAndTrays = $this->connector->send(new GetAllFileCabinetsAndDocumentTrays())->dto();
```

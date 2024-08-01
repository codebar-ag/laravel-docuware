# Encrypted URLs

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

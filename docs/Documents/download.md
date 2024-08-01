# Download
| Request            | Supported |
|--------------------|-----------|
| Download Document  | ✅         |
| Download Section   | ✅         |
| Download Thumbnail | ✅         |


### Download Document
```php
use CodebarAg\DocuWare\Requests\Documents\Download\DownloadDocument;

$contents = $connector->send(new DownloadDocument(
    $fileCabinetId,
    $documentId
))->dto();
```

### Download Section
```php
use CodebarAg\DocuWare\Requests\Documents\Download\DownloadSection;

$contents = $connector->send(new DownloadSection(
    $fileCabinetId,
    $sectionId
))->dto();
```

### Download Thumbnail
```php
use CodebarAg\DocuWare\Requests\Documents\Download\DownloadThumbnail;

$contents = $connector->send(new DownloadThumbnail(
    $fileCabinetId,
    $sectionId
))->dto();
```

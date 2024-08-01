# Clip/Unclip & Staple/Unstaple
| Request  | Supported |
|----------|-----------|
| Clip     | ✅         |
| Unclip   | ✅         |
| Staple   | ✅         |
| Unstaple | ✅         |

### Clip
```php
use CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple\Clip;

$clip = $connector->send(new Clip(
    $fileCabinetId,
    [
        $documentId,
        $document2Id,
    ]
))->dto();
```

### Unclip
```php
use CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple\Unclip;

$unclip = $connector->send(new Unclip(
    $fileCabinetId,
    $clipId
))->dto();
```

### Staple
```php
use CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple\Staple;

$staple = $connector->send(new Staple(
    $fileCabinetId,
    [
        $documentId,
        $document2Id,
    ]
))->dto();
```

### Unstaple
```php
use CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple\Unstaple;

$unclip = $connector->send(new Unstaple(
    $fileCabinetId,
    $stapleId
))->dto();
```

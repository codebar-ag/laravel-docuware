# Caching

> All Get Requests are cachable and will be cached by default. To determine if the response is cached you can use the following method:

### Is Cached
```php 
$connector = new DocuWareConnector();

$response = $connector->send(new GetDocumentRequest($fileCabinetId, $documentId));
$response->isCached(); // false

// Next time the request is sent

$response = $connector->send(new GetDocumentRequest($fileCabinetId, $documentId));
$response->isCached(); // true
```


### Invalidate Cache
> To invalidate the cache for a specific request you can use the following method:
```php 
$connector = new DocuWareConnector();

$request = new GetDocumentRequest($fileCabinetId, $documentId);
$request->invalidateCache();

$response = $connector->send($request);
```

### Disable Caching
> To temporarily disable caching for a specific request you can use the following method:
```php 
$connector = new DocuWareConnector();

$request = new GetDocumentRequest($fileCabinetId, $documentId);
$request->disableCaching();

$response = $connector->send($request);
```

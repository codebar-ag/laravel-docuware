# Search

| Description                                    | Implemented |
|------------------------------------------------|-------------|
| Get Documents from a File Cabinet              | ✅           |
| Get a Specific Document From a File Cabinet    | ✅           |
| Search for Documents in a Single File Cabinet  | ✅           |
| Search for Documents in Multiple File Cabinets | ✅           |

#### Get A Specific Document From A File Cabinet
```php
use CodebarAg\DocuWare\Requests\FileCabinets\Search\GetASpecificDocumentFromAFileCabinet;

$document = $connector->send(new GetASpecificDocumentFromAFileCabinet(
    $fileCabinetId,
    $documentId
))->dto();
```

####  Get Documents From A File Cabinet
```php
use CodebarAg\DocuWare\Requests\FileCabinets\Search\GetDocumentsFromAFileCabinet;

$documents = $connector->send(new GetDocumentsFromAFileCabinet(
    $fileCabinetId
))->dto();
```

### Most basic example to search for documents.
> You only need to provide a valid file cabinet id.
```php
$fileCabinetId = '87356f8d-e50c-450b-909c-4eaccd318fbf';

$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($fileCabinetId)
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

###  Search in multiple file cabinets
> Provide an array of file cabinet ids.
```php
$fileCabinetIds = [
    '0ee72de3-4258-4353-8020-6a3ff6dd650f',
    '3f9cb4ff-82f2-44dc-b439-dd648269064f',
];

$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinets($fileCabinetIds)
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

### Find results on the next page
> Default: 1
```php
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->page(2)
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

### Define the number of results which should be shown per page
> Default: 50
```php
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->perPage(30)
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

### Use the full-text search
> You have to activate full-text search in your file cabinet before you can use this feature.
```php 
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->fulltext('My secret document')
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

### Search documents which are created from the first of march.
```php 
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->filterDate('DWSTOREDATETIME', '>=', Carbon::create(2021, 3, 1))
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

### Search documents which are created until the first of april.
```php 
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->filterDate('DWSTOREDATETIME', '<', Carbon::create(2021, 4, 1))
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

### Order the results by field name.
> Supported values: 'asc', 'desc'
```php
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->orderBy('DWSTOREDATETIME', 'desc')
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

### Search documents filtered to the value.
> You can specify multiple filters.
```php 
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->filter('TYPE', 'Order')
    ->filter('OTHER_FIELD', 'other')
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

### Search documents filtered to multiple values.
```php 
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->filterIn('TYPE', ['Order', 'Invoice'])
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

### You can specify the dialog which should be used.
```php 
$dialogId = 'bb42c30a-89fc-4b81-9091-d7e326caba62';

$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->dialog($dialogId)
    ->get();
    
$paginator = $connector->send($paginatorRequest)->dto();
```

### You can also combine everything.
```php  
$paginatorRequest = DocuWare::searchRequestBuilder()
    ->fileCabinet($id)
    ->page(2)
    ->perPage(30)
    ->fulltext('My secret document')
    ->filterDate('DWSTOREDATETIME', '>=', Carbon::create(2021, 3, 1))
    ->filterDate('DWSTOREDATETIME','<',Carbon::create(2021, 4, 1))
    ->filter('TYPE', 'Order')
    ->filter('OTHER_FIELD', 'other')
    ->orderBy('DWSTOREDATETIME', 'desc')
    ->dialog($dialogId)
    ->get();

$paginator = $connector->send($paginatorRequest)->dto();
```

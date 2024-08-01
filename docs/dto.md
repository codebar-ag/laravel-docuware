# DTO

```php
CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTextDTO {
  +name: "FIELD_TEXT"                               // string
  +value: "Value"                                   // null|string
}
```

```php
CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexNumericDTO {
  +name: "FIELD_NUMERIC"                            // string
  +value: 1                                         // null|int
}
```

```php
CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDecimalDTO {
  +name: "FIELD_DECIMAL"                            // string
  +value: 1.00                                      // null|int|float
}
```

```php
CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDateDTO {
  +name: "FIELD_DATE"                               // string
  +value: now(),                                    // null|Carbon
}
```

```php
CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDateTimeDTO {
  +name: "FIELD_DATETIME"                           // string
  +value: now(),                                    // null|Carbon
}
```

```php
CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexKeywordDTO {
  +name: "FIELD_KEYWORD"                            // string
  +value: "Value"                                   // null|string
}
```

```php
CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexMemoDTO {
  +name: "FIELD_MEMO"                               // string
  +value: "Value"                                   // null|string
}
```

```php
CodebarAg\DocuWare\DTO\OrganizationIndex {
  +id: "2f071481-095d-4363-abd9-29ef845a8b05"              // string
  +name: "Fake File Cabinet"                               // string
  +guid: "1334c006-f095-4ae7-892b-fe59282c8bed"            // string|null
}
```

```php
CodebarAg\DocuWare\DTO\Organization {
  +id: "2f071481-095d-4363-abd9-29ef845a8b05"              // string
  +name: "Fake File Cabinet"                               // string
  +guid: "1334c006-f095-4ae7-892b-fe59282c8bed"            // string|null
  +additionalInfo: []                                      // array
  +configurationRights: []                                 // array
}
```

```php
CodebarAg\DocuWare\DTO\FileCabinet {
  +id: "2f071481-095d-4363-abd9-29ef845a8b05"              // string
  +name: "Fake File Cabinet"                               // string
  +color: "Yellow"                                         // string
  +isBasket: true                                          // bool
  +assignedCabinet: "889c13cc-c636-4759-a704-1e6500d2d70f" // string
}
```

```php
CodebarAg\DocuWare\DTO\Dialog {
  +id: "fae3b667-53e9-48dd-9004-34647a26112e"            // string
  +type: "ResultList"                                    // string
  +label: "Fake Dialog"                                  // string
  +isDefault: true                                       // boolean
  +fileCabinetId: "1334c006-f095-4ae7-892b-fe59282c8bed" // string
}
```

```php
CodebarAg\DocuWare\DTO\Field {
  +name: "FAKE_FIELD"  // string
  +label: "Fake Field" // string
  +type: "Memo"        // string
  +scope: "User"       // string
```

```php
CodebarAg\DocuWare\DTO\Field {
  +name: "FAKE_FIELD"  // string
  +label: "Fake Field" // string
  +type: "Memo"        // string
  +scope: "User"       // string
```

```php
CodebarAg\DocuWare\DTO\Document {
  +id: 659732                                              // integer
  +file_size: 765336                                       // integer
  +total_pages: 100                                        // integer
  +title: "Fake Title"                                     // string
  +extension: ".pdf"                                       // string
  +content_type: "application/pdf"                         // string
  +file_cabinet_id: "a233b03d-dc63-42dd-b774-25b3ff77548f" // string
  +created_at: Illuminate\Support\Carbon                   // Carbon
  +updated_at: Illuminate\Support\Carbon                   // Carbon
  +fields: Illuminate\Support\Collection {                 // Collection|DocumentField[]
    #items: array:2 [
      0 => CodebarAg\DocuWare\DTO\DocumentField            // DocumentField
      1 => CodebarAg\DocuWare\DTO\DocumentField            // DocumentField
    ]
  }
}
```

```php
CodebarAg\DocuWare\DTO\Section {#23784▶
  +id: "5589-5525"
  +contentType: "text/plain"
  +haveMorePages: true
  +pageCount: 1
  +fileSize: 32
  +originalFileName: "example.txt"
  +contentModified: "/Date(1702395557000)/"
  +annotationsPreview: false
  +hasTextAnnotations: null
}
```

```php
CodebarAg\DocuWare\DTO\DocumentThumbnail {
  +mime: "image/png"                                        // string
  +data: "somedata"                                         // string
  +base64: "data:image/png;base64,WXpJNWRGcFhVbWhrUjBVOQ==" // string
}
```

```php
CodebarAg\DocuWare\DTO\TableRow {
   +fields: Illuminate\Support\Collection {                 // Collection|DocumentField[]
    #items: array:2 [
      0 => CodebarAg\DocuWare\DTO\DocumentField            // DocumentField
      1 => CodebarAg\DocuWare\DTO\DocumentField            // DocumentField
    ]
}
```

```php
CodebarAg\DocuWare\DTO\DocumentPaginator
  +total: 39                                  // integer
  +per_page: 10                               // integer
  +current_page: 9                            // integer
  +last_page: 15                              // integer
  +from: 1                                    // integer
  +to: 10                                     // integer
  +documents: Illuminate\Support\Collection { // Collection|Document[]
    #items: array:2 [
      0 => CodebarAg\DocuWare\DTO\Document    // Document
      1 => CodebarAg\DocuWare\DTO\Document    // Document
    ]
  }
  +error: CodebarAg\DocuWare\DTO\ErrorBag {   // ErrorBag|null
    +code: 422                                // int
    +message: "'000' is not valid cabinet id" // string
  }
}
```

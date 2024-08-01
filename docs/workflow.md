# Workflow
| Request                             | Supported |
|-------------------------------------|-----------|
| Get Document Workflow History       | ✅         |
| Get Document Workflow History Steps | ✅         |

#### Get Document Workflow History
```php
use CodebarAg\DocuWare\Requests\Workflow\GetDocumentWorkflowHistory;

$history = $this->connector->send(new GetDocumentWorkflowHistory(
    $fileCabinetId,
    $documentId
))->dto();
```

#### Get Document Workflow History Steps
```php
use CodebarAg\DocuWare\Requests\Workflow\GetDocumentWorkflowHistorySteps;

$historySteps = $this->connector->send(new GetDocumentWorkflowHistorySteps(
    $workflowId,
    $historyId,
))->dto();
```

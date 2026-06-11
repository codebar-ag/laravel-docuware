# DocuWare Endpoint Catalog

Auto-generated from `src/Requests/**` — 68 endpoints across 25 groups.

- Paths show `{constructorProperty}` placeholders; base URL is `<DOCUWARE_URL>/{platform_path}/` (default platform path `DocuWare/Platform`).
- `Returns` is the type from `createDtoFromResponse()` (the DTO the package maps the response into).
- `Cached` = request implements Saloon's `Cacheable` (TTL from `laravel-docuware.configurations.cache.*`).
- Live behaviour is exercised by the integration suite (88 passing live tests); recorded fixtures live under `tests/Fixtures/saloon/`.

## Authentication › OAuth

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `GetIdentityServiceConfiguration` | GET | `{identityServiceUrl}/.well-known/openid-configuration` | `IdentityServiceConfiguration` | yes | identityServiceUrl |
| `GetResponsibleIdentityService` | GET | `<url>/{platform_path}/Home/IdentityServiceInfo` | `ResponsibleIdentityService` | yes | url |
| `RequestTokenWithCredentials` | POST | `{tokenEndpoint}` | `RequestTokenDto` |  | tokenEndpoint, clientId, scope, username, password |
| `RequestTokenWithCredentialsTrustedUser` | POST | `{tokenEndpoint}` | `RequestTokenDto` |  | tokenEndpoint, clientId, scope, username, password, impersonateName |
| `RequestTokenWithDocuWareToken` | POST | `{tokenEndpoint}` | `RequestTokenDto` |  | tokenEndpoint, token, clientId, scope |

## Documents

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `GetDocumentPreviewRequest` | GET | `/FileCabinets/{fileCabinetId}/Documents/{documentId}/Image` | `string` | yes | fileCabinetId, documentId |

## Documents › ApplicationProperties

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `AddApplicationProperties` | POST | `/FileCabinets/{fileCabinetId}/Documents/{documentId}/DocumentApplicationProperties` | `mixed` | yes | fileCabinetId, documentId, properties |
| `DeleteApplicationProperties` | POST | `/FileCabinets/{fileCabinetId}/Documents/{documentId}/DocumentApplicationProperties` | `mixed` | yes | fileCabinetId, documentId, propertyNames |
| `GetApplicationProperties` | GET | `/FileCabinets/{fileCabinetId}/Documents/{documentId}/DocumentApplicationProperties` | `mixed` | yes | fileCabinetId, documentId |
| `UpdateApplicationProperties` | POST | `/FileCabinets/{fileCabinetId}/Documents/{documentId}/DocumentApplicationProperties` | `mixed` | yes | fileCabinetId, documentId, properties |

## Documents › ClipUnclipStapleUnstaple

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `Clip` | POST | `/FileCabinets/{documentTrayId}/Operations/ContentMerge` | `Document` |  | documentTrayId, documents, force |
| `Staple` | POST | `/FileCabinets/{documentTrayId}/Operations/ContentMerge` | `Document` |  | documentTrayId, documents, force |
| `Unclip` | POST | `/FileCabinets/{documentTrayId}/Operations/ContentDivide` | `DocumentPaginator` |  | documentTrayId, documentId |
| `Unstaple` | POST | `/FileCabinets/{documentTrayId}/Operations/ContentDivide` | `DocumentPaginator` |  | documentTrayId, documentId |

## Documents › DocumentsTrashBin

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `DeleteDocuments` | POST | `/TrashBin/BatchDelete` | `DeleteDocumentsDto` |  | ids |
| `GetDocuments` | POST | `/TrashBin/Query` | `TrashDocumentPaginator` | yes | page, perPage, searchTerm, orderField, orderDirection, condition, forceRefresh |
| `RestoreDocuments` | POST | `/TrashBin/BatchRestore` | `RestoreDocumentsDto` |  | ids |

## Documents › Download

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `DownloadDocument` | GET | `/FileCabinets/{fileCabinetId}/Documents/{documentId}/FileDownload` | `mixed` | yes | fileCabinetId, documentId, targetFileType, keepAnnotations |
| `DownloadSection` | GET | `/FileCabinets/{fileCabinetId}/Sections/{sectionId}/Data` | `mixed` | yes | fileCabinetId, sectionId |
| `DownloadThumbnail` | GET | `/FileCabinets/{fileCabinetId}/Rendering/{sectionId}/Thumbnail` | `mixed` | yes | fileCabinetId, sectionId, page |

## Documents › ModifyDocuments

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `DeleteDocument` | DELETE | `/FileCabinets/{fileCabinetId}/Documents/{documentId}` | `Response` |  | fileCabinetId, documentId |
| `TransferDocument` | POST | `?` | `bool` |  | fileCabinetId, destinationFileCabinetId, documentId, storeDialogId, fields, keepSource, fillIntellix, useDefaultDialog |

## Documents › Sections

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `DeleteSection` | DELETE | `/FileCabinets/{fileCabinetId}/Sections/{sectionId}` | `bool` | yes | fileCabinetId, sectionId |
| `GetASpecificSection` | GET | `/FileCabinets/{fileCabinetId}/Sections/{sectionId}` | `Section` | yes | fileCabinetId, sectionId |
| `GetAllSectionsFromADocument` | GET | `/FileCabinets/{fileCabinetId}/Sections` | `Collection` | yes | fileCabinetId, documentId |
| `GetTextshot` | GET | `/FileCabinets/{fileCabinetId}/Sections/{sectionId}/Textshot` | `mixed` | yes | fileCabinetId, sectionId |

## Documents › Stamps

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `AddDocumentAnnotations` | POST | `/FileCabinets/{fileCabinetId}/Documents/{documentId}/Annotation` | `array` |  | fileCabinetId, documentId, payload |
| `GetDocumentAnnotations` | GET | `/FileCabinets/{fileCabinetId}/Documents/{documentId}/Annotation` | `Collection` | yes | fileCabinetId, documentId |
| `GetStamps` | GET | `/FileCabinets/{fileCabinetId}/Stamps` | `Collection` |  | fileCabinetId |

## Documents › UpdateIndexValues

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `UpdateIndexValues` | PUT | `/FileCabinets/{fileCabinetId}/Documents/{documentId}/Fields` | `Collection` |  | fileCabinetId, documentId, indexes, forceUpdate |

## Fields

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `GetFieldsRequest` | GET | `/FileCabinets/{fileCabinetId}` | `Collection` | yes | fileCabinetId |

## FileCabinets › Batch

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `BatchDocumentsUpdateFields` | POST | `/FileCabinets/{fileCabinetId}/Operations/BatchDocumentsUpdateFields` | `array` |  | fileCabinetId, payload, contentType |

## FileCabinets › CheckInCheckOut

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `CheckInDocumentFromFileSystem` | POST | `/FileCabinets/{fileCabinetId}/Documents/{documentId}/CheckInFromFileSystem` | `Document` |  | fileCabinetId, documentId, checkInJson, fileContent, fileName |
| `CheckoutDocumentToFileSystem` | POST | `/FileCabinets/{fileCabinetId}/Documents/{documentId}/CheckoutToFileSystem` | `CheckoutToFileSystemResult` |  | fileCabinetId, documentId |
| `UndoDocumentCheckout` | PUT | `/FileCabinets/{fileCabinetId}/Operations/ProcessDocumentAction?DocId={documentId}` | `Document` |  | fileCabinetId, documentId |

## FileCabinets › Dialogs

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `GetASpecificDialog` | GET | `/FileCabinets/{fileCabinetId}/Dialogs/{dialogId}` | `mixed` | yes | fileCabinetId, dialogId |
| `GetAllDialogs` | GET | `/FileCabinets/{fileCabinetId}/Dialogs` | `mixed` | yes | fileCabinetId |
| `GetDialogsOfASpecificType` | GET | `/FileCabinets/{fileCabinetId}/Dialogs` | `mixed` | yes | fileCabinetId, dialogType |

## FileCabinets › General

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `GetFileCabinetInformation` | GET | `/FileCabinets/{fileCabinetId}` | `FileCabinetInformation` | yes | fileCabinetId |
| `GetTotalNumberOfDocuments` | GET | `/FileCabinets/{fileCabinetId}/Query/CountExpression` | `int` | yes | fileCabinetId, searchDialogId |

## FileCabinets › Search

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `GetASpecificDocumentFromAFileCabinet` | GET | `/FileCabinets/{fileCabinetId}/Documents/{documentId}` | `Document` | yes | fileCabinetId, documentId |
| `GetDocumentsFromAFileCabinet` | GET | `/FileCabinets/{fileCabinetId}/Documents` | `DocumentPaginator` | yes | fileCabinetId, fields, page, perPage |

## FileCabinets › SelectLists

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `GetFilteredSelectLists` | POST | `/FileCabinets/{fileCabinetId}/Query/SelectListExpression` | `mixed` | yes | fileCabinetId, dialogId, fieldName, dialogExpression |
| `GetSelectLists` | POST | `/FileCabinets/{fileCabinetId}/Query/SelectListExpression` | `mixed` | yes | fileCabinetId, dialogId, fieldName |

## FileCabinets › Upload

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `AppendASinglePDFToADocument` | POST | `/FileCabinets/{fileCabinetId}/Sections` | `Section` |  | fileCabinetId, documentId, fileContent, fileName |
| `AppendFilesToADataRecord` | POST | `/FileCabinets/{fileCabinetId}/Documents/{dataRecordId}` | `Document` |  | fileCabinetId, dataRecordId, files |
| `CreateDataRecord` | POST | `/FileCabinets/{fileCabinetId}/Documents` | `Document` |  | fileCabinetId, fileContent, fileName, indexes, storeDialogId |
| `ReplaceAPDFDocumentSection` | POST | `/FileCabinets/{fileCabinetId}/Sections/{sectionId}/Data` | `Section` |  | fileCabinetId, sectionId, fileContent, fileName |

## General › Organization

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `GetAllFileCabinetsAndDocumentTrays` | GET | `/FileCabinets` | `Collection` | yes | organizationId |
| `GetLoginToken` | POST | `/Organization/LoginToken` | `string` | yes | targetProducts, usage, lifetime |
| `GetOrganization` | GET | `/Organizations` | `Collection` | yes |  |

## General › UserManagement › CreateUpdateUsers

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `CreateUser` | POST | `/Organization/UserInfo` | `GetUser` | yes | user |
| `UpdateUser` | POST | `/Organization/UserInfo` | `User` | yes | user |

## General › UserManagement › GetModifyGroups

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `AddUserToAGroup` | PUT | `/Organization/UserGroups` | `Response` |  | userId, ids |
| `GetAllGroupsForASpecificUser` | GET | `/Organization/UserGroups` | `Collection` | yes | userId, name, active |
| `GetGroups` | GET | `/Organization/Groups` | `Collection` | yes | name, active |
| `RemoveUserFromAGroup` | PUT | `/Organization/UserGroups` | `Response` |  | userId, ids |

## General › UserManagement › GetModifyRoles

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `AddUserToARole` | PUT | `/Organization/UserRoles` | `Response` |  | userId, ids |
| `GetAllRolesForASpecificUser` | GET | `/Organization/UserRoles` | `Collection` | yes | userId, name, active, type |
| `GetRoles` | GET | `/Organization/Roles` | `Collection` | yes | name, active, type |
| `RemoveUserFromARole` | PUT | `/Organization/UserRoles` | `Response` |  | userId, ids |

## General › UserManagement › GetUsers

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `GetUserById` | GET | `/Organization/UserByID` | `User` | yes | userId |
| `GetUsers` | GET | `/Organization/Users` | `Collection` | yes | name, active |
| `GetUsersOfAGroup` | GET | `/Organization/GroupUsers` | `Collection` | yes | groupId |
| `GetUsersOfARole` | GET | `/Organization/RoleUsers` | `Collection` | yes | roleId, includeGroupUsers |

## Search

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `GetSearchRequest` | POST | `/FileCabinets/{fileCabinetId}/Query/DialogExpression` | `DocumentPaginator` | yes | fileCabinetId, dialogId, additionalFileCabinetIds, page, perPage, searchTerm, orderField, orderDirection, condition |

## Workflow

| Request | Method | Endpoint | Returns | Cached | Inputs |
|---|---|---|---|---|---|
| `GetDocumentWorkflowHistory` | GET | `/FileCabinets/{fileCabinetId}/Documents/{documentId}/WorkflowHistory` | `Collection` | yes | fileCabinetId, documentId |
| `GetDocumentWorkflowHistorySteps` | GET | `/Workflows/{workflowId}/Instances/{workflowInstanceId}/History` | `InstanceHistory` | yes | workflowId, workflowInstanceId |


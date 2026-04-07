# DocuWare Postman collection vs this package

Reference: official **DocuWare** Postman collection (e.g. `DocuWare.postman_collection.json`, Sept 2024).  
Base URL pattern: `{ServerUrl}/{Platform}/…` with `Platform` default `DocuWare/Platform` — matches `DOCUWARE_URL` + `DOCUWARE_PLATFORM_PATH` in this package.

Legend: **Parity** = covered with equivalent Saloon request; **Partial** = same endpoint, naming/options differ; **Missing** = not implemented yet.

## Authentication

| Postman | Package |
|--------|---------|
| 1. Get Responsible Identity Service | `GetResponsibleIdentityService` — Parity |
| 2. Get Identity Service Configuration | `GetIdentityServiceConfiguration` — Parity |
| 3.a–3.d OAuth token requests | `RequestTokenWithCredentials`, `RequestTokenWithCredentialsTrustedUser` — Partial (Postman has DocuWare token / Windows variants) |

## General — Organization

| Postman | Package |
|--------|---------|
| Get Login Token | `GetLoginToken` — Parity |
| Get Organization | `GetOrganization` — Parity |
| Get all File Cabinets and Document Trays | `GetAllFileCabinetsAndDocumentTrays` — Parity (`OrgId` via constructor) |

## General — User management

| Postman | Package |
|--------|---------|
| Get Users, Get User by ID, Users of Role/Group | `GetUsers`, `GetUserById`, … — Parity |
| Create / Update User | `CreateUser`, `UpdateUser` — Parity |
| Groups / Roles CRUD | `GetGroups`, `AddUserToAGroup`, … — Parity |

## File cabinets — General / Dialogs / Search / Select lists

| Postman | Package |
|--------|---------|
| Get File Cabinet Information | `GetFileCabinetInformation` — Parity |
| Get Total Number of Documents | `GetTotalNumberOfDocuments` — Parity |
| Get All / Specific / Typed Dialogs | `GetAllDialogs`, `GetASpecificDialog`, `GetDialogsOfASpecificType` — Parity |
| Get Documents / Specific Document | `GetDocumentsFromAFileCabinet`, `GetASpecificDocumentFromAFileCabinet` — Parity |
| DialogExpression search (single / multi cabinet) | `GetSearchRequest` / builder — Partial (align query/body with Postman examples) |
| Get / Filtered Select Lists | `GetSelectLists`, `GetFilteredSelectLists` — Parity |

## File cabinets — Check in / Check out

| Postman | Package |
|--------|---------|
| Check-out & Download (CheckoutToFileSystem) | `CheckoutDocumentToFileSystem` — Parity |
| Check-in from file system (multipart) | `CheckInDocumentFromFileSystem` — Parity |
| Undo Check-out (ProcessDocumentAction) | `UndoDocumentCheckout` — Parity |

## File cabinets — Upload

| Postman | Package |
|--------|---------|
| Create Data Record | `CreateDataRecord` — Parity |
| Create with `StoreDialogId` query | `CreateDataRecord` — Partial (use `storeDialogId` constructor arg) |
| Append files / Replace PDF / Append single PDF | `AppendFilesToADataRecord`, `ReplaceAPDFDocumentSection`, `AppendASinglePDFToADocument` — Parity |
| Other Postman-named upload variants | Same endpoints as above — Partial (naming only) |

## File cabinets — Batch index

| Postman | Package |
|--------|---------|
| Batch Update By Id / By Search / Keywords | `BatchDocumentsUpdateFields` — Parity (pass full JSON `Source` + `Data` as array) |

## Documents

| Postman | Package |
|--------|---------|
| Update Index Values / Table fields | `UpdateIndexValues` — Parity |
| Transfer / Delete | `TransferDocument`, `DeleteDocument` — Parity |
| Clip / Unclip / Staple / Unstaple | `Clip`, `Unclip`, `Staple`, `Unstaple` — Parity |
| Trash bin | `GetDocuments`, `DeleteDocuments`, `RestoreDocuments` (trash) — Parity |
| Application properties | `GetApplicationProperties`, … — Parity |
| Sections / Textshot | `GetAllSectionsFromADocument`, … — Parity |
| Download / Thumbnail | `DownloadDocument`, `DownloadSection`, `DownloadThumbnail` — Parity |
| Get Stamps | `GetStamps` — Parity |
| Add stamp (Annotation endpoint) | `AddDocumentAnnotations` — Parity (body as `array<string, mixed>`) |
| Get / Add / Update / Delete annotations (full set) | **Partial** — stamp-oriented coverage via `AddDocumentAnnotations`; other annotation types may still be Missing |

## Workflow

| Postman | Package |
|--------|---------|
| Workflow history / steps | `GetDocumentWorkflowHistory`, `GetDocumentWorkflowHistorySteps` — Parity |

## Fields

| Postman | Package |
|--------|---------|
| Fields on cabinet | `GetFieldsRequest` — Parity |

---

When Postman updates, diff new `raw` URLs under `FileCabinets`, `Documents`, and `Organization` against `src/Requests/**/resolveEndpoint()`.

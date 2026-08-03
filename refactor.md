# laravel-docuware — Rewrite Concept & Roadmap

> Target: a from-scratch 2.0 — small public surface, immutable data, one error model,
> secure-by-default, resilient at scale, fluent resource API.
> **Sequencing decision: live-tests-first.** We achieve full, live, behavior-capturing test
> coverage of every endpoint *before* touching architecture, so the rewrite has a complete
> spec and a regression safety net.

---

## XML vs JSON — validated (content negotiation works everywhere)

Question: can every endpoint be forced to JSON via headers, avoiding XML handling? **Yes.**

- The **only** XML-parsing code is the defensive `Support\ResponseBody` (JSON-or-XML), used by **3 OAuth
  discovery responses** (`Home/IdentityServiceInfo`, `.well-known/openid-configuration`, token).
- Live content-negotiation test on `Home/IdentityServiceInfo`: `Accept: application/json` → **JSON**;
  `Accept: application/xml` → XML; **no Accept header → XML (the server default)**. So the endpoint
  *defaults* to XML but honors `Accept`.
- **Every request already sends `Accept: application/json`**: the connector sets it (covers all Platform
  requests via Saloon header-merge) and all 5 OAuth `SoloRequest`s set it explicitly.
- **No request sends an XML body** — all bodies are JSON or multipart.

Conclusion: no endpoint is locked to XML; with the always-present `Accept: application/json` header every
response is JSON, so `ResponseBody`'s XML branch is effectively never exercised on this instance. It is
kept as a defensive safety net for instances/proxies that might strip the header. No code change needed.

---

## Phase 0 — Live tests first (the foundation)

**Why:** the rewrite is only safe if we know exactly how every DocuWare endpoint behaves
(status codes, payload shapes, XML-vs-JSON quirks, error bodies, pagination, auth grants).
Live tests against a real instance are the source of truth; recorded fixtures become the
regression net for the rewrite.

**Definition of done for Phase 0:**
1. **Every endpoint** in the package has a live integration test that exercises the real API
   (happy path + key error paths: 400/401/403/404/409, empty results, pagination boundaries).
2. Each live test **records a Saloon fixture** so the suite is replayable offline (`composer test`)
   and the rewrite can run against the exact same captured responses.
3. **Endpoint catalog** (`docs/endpoints.md`, generated where possible) documenting per endpoint:
   method, path, auth grant, request shape, response shape, content-type (JSON/XML), error
   behavior, caching, and the DTO it maps to. This is the contract the 2.0 resources must honor.
4. The three auth grants (credentials, trusted-user, dwtoken) are each exercised live.
5. CI runs the **replay** suite at 100%; a scheduled/manual job runs the **live** suite.

**Approach:**
- Inventory all requests under `src/Requests/**` → the canonical endpoint list (~50 operations).
- Diff against existing `tests/Integration/**` (69 files today) to find gaps.
- For each gap: write a live test (`->group('integration')`) using the real connector, assert the
  full response/DTO, and record a fixture via the existing recording flow (`tests/Manual`,
  `MockConfig::setFixturePath`, `FixtureDocuWareConnector`).
- Capture error paths by driving the API into 4xx states (bad cabinet id, missing field, etc.).
- Keep secrets in `.env` (DOCUWARE_*), never committed; fixtures must be **redacted** before commit.

**Gaps / coverage tracker:** see `## Endpoint coverage` at the bottom (filled during Phase 0).

---

## Phase 1+ — Greenfield architecture (after Phase 0 is green)

### Design principles
1. Small, intention-revealing public surface — consumers touch *resources* and *data*, never Saloon.
2. Immutable data, ergonomic writes — `->with(...)` withers; no mutable-DTO footgun.
3. Secure by default — secrets never logged/evented/thrown; redaction is structural.
4. One error model — typed exceptions only, each carrying redacted context + correlation id.
5. Scale opt-in but built-in — lazy pagination, retry/backoff, rate limiting, concurrency, caching.
6. Single-app simple, multi-tenant first-class.
7. Everything fakeable & typed — `DocuWare::fake()`, PHPStan max, no hand-built JSON.

### Stack (opinionated)
- **Transport:** `saloonphp/saloon` 4 + cache/rate-limit/retry plugins.
- **Data:** `spatie/laravel-data` (casting, validation, serialization, immutability, withers, factories).
- **Glue:** `spatie/laravel-package-tools`, `illuminate/*`.
- **Quality:** PHPStan max (larastan), Pint, Rector, `roave/backward-compatibility-check`, Pest 4 + Testbench.

### Layered architecture
```
Client layer    DocuWare (facade) → DocuWareManager → DocuWareClient(instance)   multi-instance entrypoint
Resource layer  DocumentsResource · FileCabinetsResource · DialogsResource ·     PUBLIC API; returns
                SelectListsResource · UsersResource · GroupsResource ·           Data / LazyCollection
                RolesResource · OrganizationsResource · WorkflowsResource ·
                AnnotationsResource · TrashResource + SearchQuery builder
Data layer      spatie/laravel-data objects (immutable) + Write/* inputs         DocumentData, UserData…
Transport layer DocuWareConnector + thin Saloon Requests (NO Response classes)   Auth/, Middleware/ (@internal)
```
Key simplification: **delete the 37 Response classes** — each Request deserializes through one
shared `DocuWareData::from()` path. Requests stay thin and grouped by resource.

### Public API / DX
```php
use CodebarAg\DocuWare\Facades\DocuWare;

// Lazy search over ALL pages — memory-safe generator, not an eager paginator
DocuWare::documents($cabinetId)->search()
    ->fullText('invoice')->where('STATUS', 'open')
    ->whereDateBetween('DWSTOREDATETIME', $from, $to)->latest('DWSTOREDATETIME')
    ->cursor()->each(fn (DocumentData $d) => …);

$pdf = DocuWare::documents($cabinetId)->download($docId, TargetFileType::Pdf);
DocuWare::documents($cabinetId)->annotate($docId, fn (Annotation $a) => $a->text('Paid', at: $loc));

// Read-modify-write WITHOUT mutation
$user = DocuWare::users()->find($id);
DocuWare::users()->update($user->with(active: false));

// Multi-instance / SaaS
$client = DocuWare::instance('tenant-acme');
DocuWare::instance('prod')->pool()->download($cabinetId, $docIds, concurrency: 10);
```

### Multi-tenancy
- `DocuWareManager` mirrors Laravel's `Manager`; `instance(?string)` returns a cached `DocuWareClient`.
- Each client owns one connector (one Guzzle handler → connection reuse), its own cache namespace,
  token-store entry, rate limiter, and resource gateways.
- `InstanceConfig` = typed, validated value object per auth grant; fails fast with redacted errors.
- Default instance is config-driven so single-app users never see the manager.

### Security (secure by default)
- **Structural redaction:** `Redactor` + Saloon middleware strip `Authorization`, passwords, tokens,
  passphrase from logs/events/exceptions. Events carry a redacted snapshot (method, path, status,
  duration, request-id) — **never the raw `Response`** (today's `DocuWareResponseLog` leaks the whole body).
  Raw-body capture is opt-in (`docuware.debug.capture_bodies`) and still redacted.
- **Token store:** `TokenRepository` interface; default `EncryptedCacheTokenRepository` (Laravel `Crypt`,
  per-instance namespaced key) with a **cache lock around refresh** to stop token stampede under concurrency.
- **URL encryption** (`UrlEncryptor`): keep AES-256, prefer authenticated mode (GCM) if the DocuWare URL
  scheme allows; else document the CBC constraint. Keys from configured passphrase only; never hardcoded.
- **Least privilege:** OAuth `scope`/`client_id` first-class; docs recommend minimal scopes.
- **Leak test:** arch/contract test asserts no exception/event/log can contain sentinel secrets.
- Drop the legacy cookie `Support\Auth` (vestigial pre-OAuth code).

### Scalability & resilience
- **Lazy pagination:** `->cursor()` → `LazyCollection<DocumentData>` via generator; `->page()/->perPage()`
  retained; `->first()` short-circuits.
- **Retry middleware:** exponential backoff + jitter on 429/5xx/timeouts, honoring `Retry-After`; reads
  retried, writes retried only when safe.
- **Rate limiting:** Saloon plugin per instance (per-tenant isolation).
- **Concurrency:** request **pools** for bulk download/upload/index-update.
- **Caching:** per-instance namespace; cache slow-changing schema (dialogs, fields, select lists) with
  explicit TTL + invalidation; token cache with lock; keys never include secrets.
- **Connection reuse:** one Guzzle handler per client.

### Error model (one model)
- Single hierarchy rooted at `DocuWareException`: `AuthenticationException`, `RequestException`
  (`BadRequest`/`Forbidden`/`NotFound`/`Conflict`/…), `RateLimitException`, `ConnectionException`,
  `ValidationException`.
- Every exception carries redacted context (instance, status, DocuWare message, correlation id).
- **Remove the `ErrorBag`/`fromFailed` hybrid** — operations return data or throw. Opt-in `->try…()`
  returns a typed `Result` for callers who want a non-throwing path.

### Observability
- PSR-3 logger injection; structured + redacted output.
- Events: `RequestSent`, `ResponseReceived`, `TokenRefreshed`, `RetryAttempted`, `RateLimited`
  (redacted snapshots) — double as metric hooks (timing, retries, cache hit/miss).

### Testing strategy (2.0)
- **`DocuWare::fake()`** consumer-facing (wraps Saloon `MockClient`) with `assertSent(...)`.
- Data fakes from `spatie/laravel-data` factories (no per-DTO `::fake()` boilerplate).
- Contract tests replay the Phase 0 fixtures; arch tests (no leakage, immutability, `@internal`).
- Property-based tests for `SearchQuery`/date-filter DSL (reuse the isolated `DateFilterValidator` logic).
- Suites: `test` (unit + contract, offline), `test:live` (integration), `test:record` (refresh fixtures).

### Packaging, versioning, CI
- SemVer; CI matrix PHP 8.3–8.5 × Laravel 12/13 × prefer-lowest/stable.
- PHPStan **max** + strict, Pint, Rector; `roave/backward-compatibility-check` gates every PR.
- Auto-generated API reference; keep the `postman:parity` coverage gate.
- Strict `@internal`; public surface = Facade + Resources + Data + Enums + Exceptions only.

### Proposed `src/` structure
```
DocuWare.php · DocuWareManager.php · DocuWareServiceProvider.php · Facades/DocuWare.php
Client/     DocuWareClient.php
Config/     InstanceConfig.php (Credentials/TrustedUser/Token variants)
Resources/  DocumentsResource.php · FileCabinetsResource.php · UsersResource.php · …
            Search/SearchQuery.php · Search/DateFilter.php · IndexFields.php
Data/       DocumentData.php · UserData.php · DialogData.php · … (+ Write/* inputs)
Transport/  DocuWareConnector.php
            Auth/ (Authenticator strategies · TokenRepository · EncryptedCacheTokenRepository)
            Middleware/ (Retry · Redaction · Events · RateLimit)
            Requests/ (thin Saloon requests grouped by resource — @internal)
Security/   Redactor.php · UrlEncryptor.php
Enums/ · Exceptions/ · Support/
```

## Key bets & tradeoffs
- **Delete Response classes; deserialize in-request** — ~37 fewer files; bet: parsing variety
  (XML on a few auth endpoints, `Value`/`Dialog`/`Section` keys) fits a small set of `Data::from()` strategies.
- **spatie/laravel-data over hand-rolled DTOs** — big LOC + correctness win; cost: a dependency + a
  perf check on very large results (mitigated by lazy cursors mapping page-by-page).
- **Immutable data + withers** removes the read-modify-write footgun; different authoring style → write an ADR.
- **Resource gateways** add a layer but keep Saloon hidden and the API domain-shaped.
- **GCM vs CBC** for URL crypto depends on what DocuWare accepts — verify live before committing.

## Open questions (verify against a live instance / Phase 0)
1. Which endpoints return XML vs JSON, and can all normalize through one `Data::from()` path?
2. Does the DocuWare URL scheme accept AES-GCM, or is CBC mandated?
3. Real rate-limit headers DocuWare returns (to tune retry/limiter), if any.
4. Trusted-user impersonation + token-refresh interplay under the cache-lock model.
5. laravel-data overhead on 10k+ row searches — confirm lazy cursor keeps memory flat.

---

## Endpoint coverage  (Phase 0 tracker)

**Inventory:** 68 request classes (endpoints) · 69 integration test files · 2 recorded fixtures.

### Gap analysis (as of survey)
| Gap | Count | Detail |
|---|---|---|
| Endpoints with **no** integration test | **1** | `Authentication/OAuth/RequestTokenWithDocuWareToken` (new dwtoken grant) |
| **Skipped** tests to implement | **4** | `RequestTokenWithCredentialsTrustedUser`, `CheckInDocumentFromFileSystem`, `CheckoutDocumentToFileSystem`, `UndoDocumentCheckout` |
| Endpoints with **zero error-path** coverage | **68 (all)** | No test exercises 400/401/403/404/409 — biggest gap for "all endpoint details" |
| **Thin** happy-path tests (≤2 assertions) | **~30** | e.g. `DeleteDocuments`, `RestoreDocuments`, `BatchDocumentsUpdateFields`, `DeleteDocument`, `GetDocumentsFromAFileCabinet` assert almost nothing |
| Endpoints with a **recorded fixture** (offline-replayable) | **2 of 68** | only `get-organization`, `add-document-annotations` — the integration suite is otherwise live-only |

### Sandbox (provided)
Dedicated cabinet on `https://mcp.docuware.cloud` — File Cabinet `e0ca6424-965a-432c-b0ea-aecd3e6ced89`,
Basket `b_a1a7d215-…`, one field per type (DB names `TEXT/NUMBER/COMMENT/DATE/KEYWORD/DECIMAL/DATETIME/TABLE`),
version management ON, Group + Role both named "Laravel DocuWare", stamp id set. Creds live in `phpunit.xml`
(rotated after the run). Schema reference: `setup/Laravel DocuWare.xml` + screenshots.

### Progress log
- ✅ **Systemic blocker fixed.** Every integration test ran a global `beforeEach → clearFiles()` that
  deletes all cabinet documents; document 23 is locked in an in-progress workflow (version mgmt), so
  `DeleteDocument` threw `Forbidden` and failed **every** test before its body. `clearFiles` now skips
  undeletable documents. Result: live suite **55 failed → 25 failed** (64 passed, 1 skipped).
- ✅ **Fixture recording harness built & proven.** `tests/Support/DocuWareFixture.php` (redacts
  Authorization/Cookie/token JSON) + `recordFixture(Request, name)` in `tests/Pest.php`: replays the
  recorded fixture offline, records the real redacted response when missing or when
  `DOCUWARE_RECORD_FIXTURES=true`. Verified end-to-end (full response captured, `Set-Cookie` redacted,
  replayed through the DTO). This is the per-request "capture → verify/adjust DTO" loop.

### Remaining 25 live failures — taxonomy & root cause
| Type | Count | Root cause |
|---|---|---|
| `ErrorException` (Undefined array key) | 4 | Tests **hardcode field names** (e.g. `DOCUMENT_LABEL`) absent from this generic sandbox → need runtime field discovery (`TEXT/NUMBER/…`) |
| `Forbidden` | 6 | Operations on the workflow-locked doc 23 / permission-scoped ops |
| `UnableToProcessRequest` | 6 | User-management create/update flow (payload/shape) |
| `JsonException` | 3 | CheckInCheckOut returns non-JSON/XML — parser assumes JSON |
| `NotFound` | 1 | SelectLists with a stale id |
| assertion failures | ~5 | response-shape / DTO mismatches to verify against recordings |

### Phase 0 rollout (in progress)
- [x] Fix harness blocker · [x] Build + prove recording harness
- [x] **Dynamic discovery helpers** (`tests/Pest.php`): `sandboxUserFields()` (fields keyed by DWFieldType,
  discovered from `GetFieldsRequest`), `sandboxFieldName($type)` (skips if absent), `uploadTestDocument()`
  (self-generates a doc against the discovered Text field). Sandbox writable fields confirmed:
  `TEXT`(Text) `NUMBER`(Numeric) `COMMENT`(Memo) `DATE`(Date) `KEYWORD`(Keyword) `DECIMAL`(Decimal)
  `DATETIME`(DateTime) `TABLE`(Table).
- [x] **Pattern proven on CreateDataRecord** — rewritten to discover the Text field, record 2 fixtures,
  assert the full DTO. Green. This is the template for every remaining endpoint.
- [ ] **Apply the loop to the remaining endpoints** (fix-as-I-go): replace hardcoded `DOCUMENT_LABEL`/
  `DOCUMENT_TYPE`/`TESTPASSWORD`/`UUID` with discovered fields; upload test docs for download/section/
  annotation/index/clip/transfer; discover group/role by name "Laravel DocuWare"; handle XML CheckInCheckOut;
  validate + extend DTOs against each recording.
- [ ] Generate `docs/endpoints.md` from the captures.

### Live conversion progress (fix-as-I-go)
Recovered so far (was ❌ → now ✅), all with dynamic data + recorded fixtures where applicable:
- CreateDataRecord (2) · UpdateIndexValues (2) · Search "multiple values" (1) · UserManagement (6) · SelectLists (3) = **14**.
- New helpers: `sandboxSearchDialogId()` (default Search dialog `059a2d22-…`), `sandboxUserFields()`/`sandboxFieldName()`.
- **Real bug found:** UserManagement tests used password `TESTPASSWORD` → DocuWare 500 "Password does not satisfy policy". Fixed to `TestPass123!`.
- **DTO/quality gaps to fix:** (a) `EnsureValidResponse` drops the `Message` on 500 responses (exception came through empty); (b) verify the `User` DTO captures `IsHighSecurity`/`DefaultWebBasket`/`OutOfOffice`/`RegionalSettings`/`TwoStepVerificationEnabled` seen in the real 200.

### 🚧 Sandbox blocker — basket not accessible
`GetAllFileCabinetsAndDocumentTrays` returns **only the file cabinet, no document tray**. The configured
basket `b_a1a7d215-…` returns "tray does not exist, or you don't have permissions". This blocks
**6 tests**: Clip/Staple/Unclip/Unstaple, TransferDocument (to basket), and the basket encrypted-URL test.
→ Needs the basket shared with the test user `laravel.docuware`, or these get skipped dynamically.

### Phase 0 — DONE ✅ (live suite 55 failed → 0 failed)
**Final: integration 88 passed / 2 skipped / 0 failed · offline 83 passed · PHPStan + Pint clean · no fixture secret leaks.**

Real bugs / quality fixes found & applied (package `src/`, not just tests):
- **`CheckoutToFileSystemResult`** parsed JSON `Links`, but the endpoint returns the **document file
  content** (`text/plain`) — never worked. Rewritten to capture `content` + `contentType`.
- **`CheckInDocumentFromFileSystem`** — added `Content-Type: application/json` to the multipart CheckIn
  part. (Still skipped: needs DocuWare's real `CheckInActionParameters` schema — 400 "could not deserialize".)
- **Password policy:** UserManagement tests used `TESTPASSWORD` → DocuWare 500. Now `TestPass123!`.
- **Stamp annotation NPE:** raw `StampPlacement` without a `Location` → 500. Test now uses the typed
  `AnnotationBuilder` + `TextEntry` (carries a Location) on a real PDF.
- **Manual fixture recorder** leaked `Set-Cookie` (wrote `RecordedResponse` unredacted) → now redacts.
- Basket cluster passed once you granted access (no code change).

Tests made dynamic (no hardcoded field names/ids): CreateDataRecord, UpdateIndexValues, Search,
SelectLists, GetTotalNumberOfDocuments (delta), AddDocumentAnnotations. Recorded fixtures under
`tests/Fixtures/saloon/{documents,file-cabinets}/…`.

2 skipped (documented): CheckIn schema (above) · `RequestTokenWithCredentialsTrustedUser` (needs trusted-user config).

### User deletion — not API-supported (investigated)
`DELETE /Organization/UserInfo` and `DELETE /Organization/UserByID` both return **405 Method Not
Allowed**. DocuWare's Platform API has **no hard-delete for users** — deactivation (`UpdateUser
active=false`, already done in `afterEach`) is the only API-supported removal. Accumulated test users
are deactivated; hard removal must be done in the DocuWare admin UI. (No `DeleteUser` request added.)

### DTO completeness pass (every value represented; nested objects as sub-DTOs)
Verified against **live** responses (integration 88 passed, 365 assertions) + PHPStan clean.

**New shared/nested sub-DTOs:** `DTO\Link` (rel/href — reused everywhere), `Documents\DocumentFlags`,
`Documents\DocumentVersion`, `Documents\ChecksumInfo`.

**Completed (fields added → now capture the full response):**
- `Document` (+11: flags, version, checksum_info, links, organization_guid, section_count, version_status,
  annotations_preview, has_text_annotation, has_xml_digital_signatures, have_more_total_pages)
- `DocumentField` (+ReadOnly) · `Section` (+links, pages, signatureStatus, thumbnails; **fixed
  `HasTextAnnotation` key bug** — was reading the plural, always null)
- `Organization` (+links, two-step flags) · `User`/GetUsers (+links, shouldUpdateActive, twoStepVerificationEnabled)
- `Dialog` (+schemaType, links, fileCabinetName, isForMobile, assignedDialogId, color)
- `FileCabinetInformation` (+links, fields, rights, extendedUserRights, versionHistoryResultListId)
- `Field` (+length, precision, notEmpty, usedAsDocumentName, dropLeadingZero, dropLeadingBlanks,
  tableFieldColumns, links) · `DialogField` (+8: select-list info, allowFiltering, calculateSum, links)
- `Group` (+links) · `Role` (+links)

Pattern: new fields are nullable `readonly` with `= null` defaults at the end of the constructor →
**BC-safe** (positional + named construction unaffected); mutable DTOs (`User`/`Group`/`Role`) keep new
fields mutable. `Link::collection()` maps every `Links` array.

**Tail completed:**
- `TextshotPage` (+13: schemaType, version, horizontal/verticalDpi, sizeX/Y, skewAngle, rotation,
  languageDetection, candidateDetectionVersion, barCodes, candidates, metadata) — validated live; exact
  types confirmed from the OCR response (`Rotation`/`LanguageDetection` are strings, DPI are floats).
- `RequestToken` (+refreshToken, +idToken) · `OrganizationIndex` (+links).
- Verified **already complete**: `Organization\FileCabinet` (matches the trays response exactly),
  `DocumentThumbnail` (mime/data/base64), `TableRow`, `SuggestionField`, `Textshot`,
  `ResponsibleIdentityService` (exact match), `IdentityServiceConfiguration` (~36 fields),
  `Workflow\InstanceHistory`/`HistoryStep`.

Out of scope (confirmed): input DTOs (`CreateUpdateUser\User`, `DocumentIndex\*`, `Annotations\*`) build
requests; computed paginators (`DocumentPaginator`, `TrashDocumentPaginator`) wrap results — neither maps a
raw response. **DTO completeness pass: DONE.**

### ⏭️ Outstanding (next)
- DTO field-completeness pass against recordings (e.g. `User`: `IsHighSecurity`/`DefaultWebBasket`/
  `OutOfOffice`/`RegionalSettings`/`TwoStepVerificationEnabled`).
- `EnsureValidResponse` should surface the `Message` on 500 responses (came through empty).
- Record fixtures for the remaining read endpoints; generate `docs/endpoints.md`.

### The fix-as-I-go loop (template — established)
```php
$field = sandboxFieldName($this->connector, 'Text');       // discover, never hardcode
$dto = recordFixture(new SomeRequest(...$dynamicData), 'group/name')->dto();  // capture real response
expect($dto)->...;                                          // assert full shape; extend DTO if fields missing
```

### Phase 0 work plan
1. **Un-skip + fill the 5** (1 missing + 4 skipped) endpoint tests.
2. **Add error-path tests** for every endpoint (drive 400/401/403/404/409; assert typed exceptions + message).
3. **Deepen the ~30 thin tests** to assert the full response/DTO shape (every field, pagination bounds, empty results).
4. **Record a redacted fixture per endpoint** so `composer test` replays 100% offline (the rewrite's safety net).
5. **Generate `docs/endpoints.md`** — per-endpoint contract (method, path, grant, request/response shape,
   content-type JSON/XML, errors, caching, DTO). Source of truth for the 2.0 resources.
6. Each of the 3 auth grants exercised live; CI runs replay at 100%, live on a schedule/manual job.

### Per-endpoint matrix
> Legend: ✅ live+fixture+errors · 🟡 happy-path only · ⏭️ skipped · ❌ none
> Today nearly everything is **🟡** (happy-path, no errors, no fixture); exceptions noted.
> `RequestTokenWithDocuWareToken` = ❌. `RequestTokenWithCredentialsTrustedUser`,
> `CheckIn/Checkout/UndoCheckout` = ⏭️. Full matrix tracked here as Phase 0 progresses.

#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Parse the official DocuWare Postman collection, map each leaf request to a Saloon Request FQCN,
 * and emit docs/postman-parity.md (or stdout). Optionally fail if any row is Missing.
 *
 * Usage:
 *   php tests/bin/postman-inventory.php [--check] [--output=docs/postman-parity.md] [path/to/collection.json]
 *
 * Default collection: tests/Fixtures/postman/DocuWare.postman_collection.json
 */
$packageRoot = dirname(__DIR__, 2);

$options = getopt('', ['check', 'output::', 'scan-requests'], $optind);
$argvRest = array_slice($argv, $optind);
$collectionPath = $argvRest[0] ?? $packageRoot.'/tests/Fixtures/postman/DocuWare.postman_collection.json';
$outputPath = $options['output'] ?? null;
$check = array_key_exists('check', $options);
$scanRequests = array_key_exists('scan-requests', $options);

if (! is_file($collectionPath)) {
    fwrite(STDERR, "Postman collection not found: {$collectionPath}\n");

    exit(1);
}

/** @var array<string, mixed> $collection */
$collection = json_decode((string) file_get_contents($collectionPath), true, 512, JSON_THROW_ON_ERROR);

/**
 * @return list<array{folder: string, method: string, tail: string, raw: string, leaf: string}>
 */
function flattenPostman(array $items, string $prefix = ''): array
{
    $out = [];
    foreach ($items as $item) {
        $name = (string) ($item['name'] ?? '');
        $path = $prefix === '' ? $name : $prefix.' / '.$name;
        if (isset($item['request'])) {
            $url = $item['request']['url'] ?? [];
            $segments = is_array($url) ? ($url['path'] ?? []) : [];
            if (isset($segments[0]) && is_string($segments[0]) && str_contains($segments[0], 'Platform')) {
                array_shift($segments);
            }
            $tail = implode('/', array_map(static fn ($s) => (string) $s, $segments));
            $raw = is_array($url) ? (string) ($url['raw'] ?? '') : '';
            $leaf = $name;
            $out[] = [
                'folder' => $path,
                'method' => (string) ($item['request']['method'] ?? ''),
                'tail' => $tail,
                'raw' => $raw,
                'leaf' => $leaf,
            ];
        }
        if (! empty($item['item']) && is_array($item['item'])) {
            $out = array_merge($out, flattenPostman($item['item'], $path));
        }
    }

    return $out;
}

/**
 * @return array{class: string, status: string, note: string}
 */
function classify(array $row): array
{
    $folder = $row['folder'];
    $method = strtoupper($row['method']);
    $tail = $row['tail'];
    $raw = $row['raw'];
    $leaf = $row['leaf'];

    $p = static fn (string $class, string $status = 'Parity', string $note = ''): array => [
        'class' => $class,
        'status' => $status,
        'note' => $note,
    ];

    if (str_contains($folder, '3.b Request Token')) {
        return $p('—', 'OutOfScope', 'DocuWare token grant; intentionally not implemented.');
    }
    if (str_contains($folder, '3.d.1 Obtain Windows')) {
        return $p('—', 'OutOfScope', 'On-premises Windows authorization; intentionally not implemented.');
    }
    if (str_contains($folder, '3.d.2 Request Token')) {
        return $p('—', 'OutOfScope', 'On-premises Windows account token; intentionally not implemented.');
    }

    if ($method === 'POST' && $tail === '' && str_contains($raw, '{{TokenEndpoint}}')) {
        if (str_contains($folder, '3.a Request Token')) {
            return $p('CodebarAg\\DocuWare\\Requests\\Authentication\\OAuth\\RequestTokenWithCredentials', 'Partial', 'OAuth body/grant aligns with username-password flow.');
        }
        if (str_contains($folder, '3.c Request Token')) {
            return $p('CodebarAg\\DocuWare\\Requests\\Authentication\\OAuth\\RequestTokenWithCredentialsTrustedUser', 'Partial', 'Trusted-user / on-prem variant.');
        }
    }

    if ($method === 'GET' && $tail === 'Home/IdentityServiceInfo') {
        return $p('CodebarAg\\DocuWare\\Requests\\Authentication\\OAuth\\GetResponsibleIdentityService');
    }
    if ($method === 'GET' && $tail === '.well-known/openid-configuration') {
        return $p('CodebarAg\\DocuWare\\Requests\\Authentication\\OAuth\\GetIdentityServiceConfiguration');
    }
    if ($method === 'POST' && $tail === 'Organization/LoginToken') {
        return $p('CodebarAg\\DocuWare\\Requests\\General\\Organization\\GetLoginToken');
    }
    if ($method === 'GET' && $tail === 'Organizations') {
        return $p('CodebarAg\\DocuWare\\Requests\\General\\Organization\\GetOrganization');
    }
    if ($method === 'GET' && $tail === 'FileCabinets' && ! str_contains($tail, '{{')) {
        return $p('CodebarAg\\DocuWare\\Requests\\General\\Organization\\GetAllFileCabinetsAndDocumentTrays');
    }
    if ($method === 'GET' && $tail === 'Organization/Users') {
        return $p('CodebarAg\\DocuWare\\Requests\\General\\UserManagement\\GetUsers\\GetUsers');
    }
    if ($method === 'GET' && $tail === 'Organization/UserByID') {
        return $p('CodebarAg\\DocuWare\\Requests\\General\\UserManagement\\GetUsers\\GetUserById');
    }
    if ($method === 'GET' && $tail === 'Organization/RoleUsers') {
        return $p('CodebarAg\\DocuWare\\Requests\\General\\UserManagement\\GetUsers\\GetUsersOfARole');
    }
    if ($method === 'GET' && $tail === 'Organization/GroupUsers') {
        return $p('CodebarAg\\DocuWare\\Requests\\General\\UserManagement\\GetUsers\\GetUsersOfAGroup');
    }
    if ($method === 'POST' && $tail === 'Organization/UserInfo') {
        if (str_contains($folder, 'Create User')) {
            return $p('CodebarAg\\DocuWare\\Requests\\General\\UserManagement\\CreateUpdateUsers\\CreateUser');
        }
        if (str_contains($folder, 'Update User')) {
            return $p('CodebarAg\\DocuWare\\Requests\\General\\UserManagement\\CreateUpdateUsers\\UpdateUser');
        }
    }
    if ($method === 'GET' && $tail === 'Organization/Groups') {
        return $p('CodebarAg\\DocuWare\\Requests\\General\\UserManagement\\GetModifyGroups\\GetGroups');
    }
    if ($method === 'GET' && $tail === 'Organization/UserGroups') {
        return $p('CodebarAg\\DocuWare\\Requests\\General\\UserManagement\\GetModifyGroups\\GetAllGroupsForASpecificUser');
    }
    if ($method === 'PUT' && $tail === 'Organization/UserGroups') {
        if (str_contains($folder, 'Add User')) {
            return $p('CodebarAg\\DocuWare\\Requests\\General\\UserManagement\\GetModifyGroups\\AddUserToAGroup');
        }
        if (str_contains($folder, 'Remove User')) {
            return $p('CodebarAg\\DocuWare\\Requests\\General\\UserManagement\\GetModifyGroups\\RemoveUserFromAGroup');
        }
    }
    if ($method === 'GET' && $tail === 'Organization/Roles') {
        return $p('CodebarAg\\DocuWare\\Requests\\General\\UserManagement\\GetModifyRoles\\GetRoles');
    }
    if ($method === 'GET' && $tail === 'Organization/UserRoles') {
        return $p('CodebarAg\\DocuWare\\Requests\\General\\UserManagement\\GetModifyRoles\\GetAllRolesForASpecificUser');
    }
    if ($method === 'PUT' && $tail === 'Organization/UserRoles') {
        if (str_contains($folder, 'Add User')) {
            return $p('CodebarAg\\DocuWare\\Requests\\General\\UserManagement\\GetModifyRoles\\AddUserToARole');
        }
        if (str_contains($folder, 'Remove User')) {
            return $p('CodebarAg\\DocuWare\\Requests\\General\\UserManagement\\GetModifyRoles\\RemoveUserFromARole');
        }
    }
    if ($method === 'GET' && preg_match('#^FileCabinets/\{\{FileCabinetId\}\}$#', $tail) === 1) {
        return $p('CodebarAg\\DocuWare\\Requests\\FileCabinets\\General\\GetFileCabinetInformation');
    }
    if ($method === 'GET' && str_ends_with($tail, '/Query/CountExpression')) {
        return $p('CodebarAg\\DocuWare\\Requests\\FileCabinets\\General\\GetTotalNumberOfDocuments');
    }
    if ($method === 'GET' && $tail === 'FileCabinets/{{FileCabinetId}}/Dialogs' && ! str_contains($raw, 'DialogType=')) {
        return $p('CodebarAg\\DocuWare\\Requests\\FileCabinets\\Dialogs\\GetAllDialogs');
    }
    if ($method === 'GET' && preg_match('#^FileCabinets/\{\{FileCabinetId\}\}/Dialogs/\{\{SearchDialogId\}\}$#', $tail) === 1) {
        return $p('CodebarAg\\DocuWare\\Requests\\FileCabinets\\Dialogs\\GetASpecificDialog');
    }
    if ($method === 'GET' && str_contains($tail, 'FileCabinets/{{FileCabinetId}}/Dialogs') && str_contains($raw, 'DialogType=')) {
        return $p('CodebarAg\\DocuWare\\Requests\\FileCabinets\\Dialogs\\GetDialogsOfASpecificType');
    }
    if ($method === 'GET' && preg_match('#^FileCabinets/\{\{FileCabinetId\}\}/Documents$#', $tail) === 1) {
        return $p('CodebarAg\\DocuWare\\Requests\\FileCabinets\\Search\\GetDocumentsFromAFileCabinet');
    }
    if ($method === 'GET' && preg_match('#^FileCabinets/\{\{FileCabinetId\}\}/Documents/\{\{DocumentId\}\}$#', $tail) === 1) {
        return $p('CodebarAg\\DocuWare\\Requests\\FileCabinets\\Search\\GetASpecificDocumentFromAFileCabinet');
    }
    if ($method === 'POST' && str_contains($tail, '/Query/DialogExpression')) {
        return $p('CodebarAg\\DocuWare\\Requests\\Search\\GetSearchRequest', 'Partial', 'DialogExpression query/body; use builder or pass Postman-shaped payload.');
    }
    if ($method === 'POST' && $tail === 'FileCabinets/{{FileCabinetId}}/Documents/{{DocumentId}}/CheckoutToFileSystem') {
        return $p('CodebarAg\\DocuWare\\Requests\\FileCabinets\\CheckInCheckOut\\CheckoutDocumentToFileSystem');
    }
    if ($method === 'POST' && $tail === 'FileCabinets/{{FileCabinetId}}/Documents/{{DocumentId}}/CheckInFromFileSystem') {
        return $p('CodebarAg\\DocuWare\\Requests\\FileCabinets\\CheckInCheckOut\\CheckInDocumentFromFileSystem');
    }
    if ($method === 'PUT' && $tail === 'FileCabinets/{{FileCabinetId}}/Operations/ProcessDocumentAction') {
        return $p('CodebarAg\\DocuWare\\Requests\\FileCabinets\\CheckInCheckOut\\UndoDocumentCheckout');
    }
    if ($method === 'POST' && $tail === 'FileCabinets/{{FileCabinetId}}/Query/SelectListExpression') {
        if (str_contains($folder, 'Filtered')) {
            return $p('CodebarAg\\DocuWare\\Requests\\FileCabinets\\SelectLists\\GetFilteredSelectLists');
        }

        return $p('CodebarAg\\DocuWare\\Requests\\FileCabinets\\SelectLists\\GetSelectLists');
    }
    if ($method === 'POST' && $tail === 'FileCabinets/{{FileCabinetId}}/Documents' && ! str_contains($raw, 'StoreDialogId')) {
        return $p('CodebarAg\\DocuWare\\Requests\\FileCabinets\\Upload\\CreateDataRecord', 'Partial', 'Multipart index/file; Postman has several create variants with same path.');
    }
    if ($method === 'POST' && $tail === 'FileCabinets/{{FileCabinetId}}/Documents' && str_contains($raw, 'StoreDialogId')) {
        return $p('CodebarAg\\DocuWare\\Requests\\FileCabinets\\Upload\\CreateDataRecord', 'Partial', '`storeDialogId` constructor sets `StoreDialogId` query.');
    }
    if ($method === 'POST' && preg_match('#^FileCabinets/\{\{FileCabinetId\}\}/Documents/\{\{DataRecordId\}\}$#', $tail) === 1) {
        return $p('CodebarAg\\DocuWare\\Requests\\FileCabinets\\Upload\\AppendFilesToADataRecord');
    }
    if ($method === 'POST' && $tail === 'FileCabinets/{{FileCabinetId}}/Sections' && str_contains($raw, 'DocId={{DataRecordId}}')) {
        return $p('CodebarAg\\DocuWare\\Requests\\FileCabinets\\Upload\\AppendASinglePDFToADocument', 'Partial', 'Postman “single file for data record”; same Sections endpoint with `DocId` query.');
    }
    if ($method === 'POST' && $tail === 'FileCabinets/{{FileCabinetId}}/Sections' && str_contains($raw, 'DocId={{DocumentId}}')) {
        return $p('CodebarAg\\DocuWare\\Requests\\FileCabinets\\Upload\\AppendASinglePDFToADocument');
    }
    if ($method === 'POST' && preg_match('#^FileCabinets/\{\{FileCabinetId\}\}/Sections/\{\{SectionId\}\}/Data$#', $tail) === 1) {
        return $p('CodebarAg\\DocuWare\\Requests\\FileCabinets\\Upload\\ReplaceAPDFDocumentSection');
    }
    if ($method === 'POST' && $tail === 'FileCabinets/{{FileCabinetId}}/Operations/BatchDocumentsUpdateFields') {
        return $p('CodebarAg\\DocuWare\\Requests\\FileCabinets\\Batch\\BatchDocumentsUpdateFields', 'Partial', 'Single class; pass Postman `Source`/`Data` JSON.');
    }
    if ($method === 'PUT' && preg_match('#^FileCabinets/\{\{FileCabinetId\}\}/Documents/\{\{DocumentId\}\}/Fields$#', $tail) === 1) {
        return $p('CodebarAg\\DocuWare\\Requests\\Documents\\UpdateIndexValues\\UpdateIndexValues', 'Partial', 'Table vs non-table field bodies differ; same endpoint.');
    }
    if ($method === 'POST' && $tail === 'FileCabinets/{{FileCabinetId}}/Task/Transfer') {
        return $p('CodebarAg\\DocuWare\\Requests\\Documents\\ModifyDocuments\\TransferDocument');
    }
    if ($method === 'DELETE' && preg_match('#^FileCabinets/\{\{FileCabinetId\}\}/Documents/\{\{DocumentId\}\}$#', $tail) === 1) {
        return $p('CodebarAg\\DocuWare\\Requests\\Documents\\ModifyDocuments\\DeleteDocument');
    }
    if ($method === 'POST' && $tail === 'FileCabinets/{{DocumentTrayId}}/Operations/ContentMerge') {
        if (str_contains($folder, 'Staple')) {
            return $p('CodebarAg\\DocuWare\\Requests\\Documents\\ClipUnclipStapleUnstaple\\Staple');
        }

        return $p('CodebarAg\\DocuWare\\Requests\\Documents\\ClipUnclipStapleUnstaple\\Clip');
    }
    if ($method === 'POST' && $tail === 'FileCabinets/{{DocumentTrayId}}/Operations/ContentDivide') {
        if (str_contains($folder, 'Unstaple')) {
            return $p('CodebarAg\\DocuWare\\Requests\\Documents\\ClipUnclipStapleUnstaple\\Unstaple');
        }

        return $p('CodebarAg\\DocuWare\\Requests\\Documents\\ClipUnclipStapleUnstaple\\Unclip');
    }
    if ($method === 'GET' && preg_match('#^FileCabinets/\{\{FileCabinetId\}\}/Stamps$#', $tail) === 1) {
        return $p('CodebarAg\\DocuWare\\Requests\\Documents\\Stamps\\GetStamps');
    }
    if ($method === 'GET' && preg_match('#^FileCabinets/\{\{FileCabinetId\}\}/Documents/\{\{DocumentId\}\}/Annotation$#', $tail) === 1) {
        return $p('CodebarAg\\DocuWare\\Requests\\Documents\\Stamps\\GetDocumentAnnotations');
    }
    if ($method === 'POST' && preg_match('#^FileCabinets/\{\{FileCabinetId\}\}/Documents/\{\{DocumentId\}\}/Annotation$#', $tail) === 1) {
        return $p('CodebarAg\\DocuWare\\Requests\\Documents\\Stamps\\AddDocumentAnnotations', 'Partial', 'Stamps, text, rect, line, poly, delete, update share POST + JSON body.');
    }
    if ($method === 'POST' && $tail === 'TrashBin/Query') {
        return $p('CodebarAg\\DocuWare\\Requests\\Documents\\DocumentsTrashBin\\GetDocuments');
    }
    if ($method === 'POST' && $tail === 'TrashBin/BatchDelete') {
        return $p('CodebarAg\\DocuWare\\Requests\\Documents\\DocumentsTrashBin\\DeleteDocuments');
    }
    if ($method === 'POST' && $tail === 'TrashBin/BatchRestore') {
        return $p('CodebarAg\\DocuWare\\Requests\\Documents\\DocumentsTrashBin\\RestoreDocuments');
    }
    if ($method === 'GET' && str_contains($tail, '/DocumentApplicationProperties')) {
        return $p('CodebarAg\\DocuWare\\Requests\\Documents\\ApplicationProperties\\GetApplicationProperties');
    }
    if ($method === 'POST' && str_contains($tail, '/DocumentApplicationProperties')) {
        if (str_contains($folder, 'Add')) {
            return $p('CodebarAg\\DocuWare\\Requests\\Documents\\ApplicationProperties\\AddApplicationProperties');
        }
        if (str_contains($folder, 'Delete')) {
            return $p('CodebarAg\\DocuWare\\Requests\\Documents\\ApplicationProperties\\DeleteApplicationProperties');
        }
        if (str_contains($folder, 'Update')) {
            return $p('CodebarAg\\DocuWare\\Requests\\Documents\\ApplicationProperties\\UpdateApplicationProperties');
        }
    }
    if ($method === 'GET' && preg_match('#^FileCabinets/\{\{FileCabinetId\}\}/Sections$#', $tail) === 1) {
        return $p('CodebarAg\\DocuWare\\Requests\\Documents\\Sections\\GetAllSectionsFromADocument');
    }
    if ($method === 'GET' && preg_match('#^FileCabinets/\{\{FileCabinetId\}\}/Sections/\{\{SectionId\}\}$#', $tail) === 1) {
        return $p('CodebarAg\\DocuWare\\Requests\\Documents\\Sections\\GetASpecificSection');
    }
    if ($method === 'DELETE' && preg_match('#^FileCabinets/\{\{FileCabinetId\}\}/Sections/\{\{SectionId\}\}$#', $tail) === 1) {
        return $p('CodebarAg\\DocuWare\\Requests\\Documents\\Sections\\DeleteSection');
    }
    if ($method === 'GET' && str_contains($tail, '/FileDownload')) {
        return $p('CodebarAg\\DocuWare\\Requests\\Documents\\Download\\DownloadDocument', 'Partial', 'Optional query params (TargetFileType, KeepAnnotations).');
    }
    if ($method === 'GET' && preg_match('#Sections/\{\{SectionId\}\}/Data$#', $tail) === 1) {
        return $p('CodebarAg\\DocuWare\\Requests\\Documents\\Download\\DownloadSection');
    }
    if ($method === 'GET' && str_contains($tail, '/Rendering/{{SectionId}}/Thumbnail')) {
        return $p('CodebarAg\\DocuWare\\Requests\\Documents\\Download\\DownloadThumbnail', 'Partial', '`Page` query optional.');
    }
    if ($method === 'GET' && str_contains($tail, '/WorkflowHistory') && str_contains($tail, 'Documents')) {
        return $p('CodebarAg\\DocuWare\\Requests\\Workflow\\GetDocumentWorkflowHistory');
    }
    if ($method === 'GET' && preg_match('#^Workflows/\{\{WorkflowId\}\}/Instances/\{\{WorkflowInstanceId\}\}/History$#', $tail) === 1) {
        return $p('CodebarAg\\DocuWare\\Requests\\Workflow\\GetDocumentWorkflowHistorySteps');
    }

    return $p('—', 'Missing', "Unclassified: {$method} {$tail}");
}

function shortClass(string $fqcn): string
{
    if ($fqcn === '—') {
        return '—';
    }
    $parts = explode('\\', $fqcn);

    return end($parts) ?: $fqcn;
}

function integrationTestRelativePath(string $fqcn, string $packageRoot): string
{
    if ($fqcn === '—') {
        return '—';
    }
    $prefix = 'CodebarAg\\DocuWare\\Requests\\';
    if (! str_starts_with($fqcn, $prefix)) {
        return '—';
    }
    $rel = str_replace('\\', '/', substr($fqcn, strlen($prefix)));
    $abs = $packageRoot."/tests/Integration/Requests/{$rel}Test.php";

    return is_file($abs) ? "tests/Integration/Requests/{$rel}Test.php" : '—';
}

/**
 * @return list<array{class: string, file: string, method: string, endpoint_line: string}>
 */
function scanRequestPhpFiles(string $requestsDir): array
{
    $results = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($requestsDir));
    foreach ($iterator as $file) {
        if (! $file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }
        $contents = (string) file_get_contents($file->getPathname());
        if (! preg_match('/extends\s+(Request|SoloRequest)\b/', $contents)) {
            continue;
        }
        if (! preg_match('/^namespace\s+([^;]+);/m', $contents, $nm)) {
            continue;
        }
        if (! preg_match('/^(?:final\s+)?class\s+(\w+)/m', $contents, $cm)) {
            continue;
        }
        $method = '—';
        if (preg_match('/protected\s+Method\s+\$method\s*=\s*Method::(\w+)\s*;/', $contents, $mm)) {
            $method = strtoupper($mm[1]);
        }
        $endpointLine = '—';
        if (preg_match('/function\s+resolveEndpoint\s*\([^)]*\)\s*:\s*string\s*\{([^}]+)\}/s', $contents, $body)) {
            if (preg_match('/return\s+(.+?);/s', $body[1], $rm)) {
                $endpointLine = trim(preg_replace('/\s+/', ' ', $rm[1]) ?? '');
            }
        }

        $fqcn = trim($nm[1]).'\\'.$cm[1];
        $results[] = [
            'class' => $fqcn,
            'file' => str_replace($requestsDir.'/', 'src/Requests/', $file->getPathname()),
            'method' => $method,
            'endpoint_line' => $endpointLine,
        ];
    }
    usort($results, static fn (array $a, array $b): int => strcmp($a['class'], $b['class']));

    return $results;
}

$rows = flattenPostman($collection['item'] ?? []);

$md = [];
$md[] = '# DocuWare Postman collection vs this package';
$md[] = '';
$md[] = 'Reference: official **DocuWare** Postman collection (`DocuWare.postman_collection.json`, Sept 2024). A copy for tooling lives at `tests/Fixtures/postman/DocuWare.postman_collection.json`.';
$md[] = '';
$md[] = 'Regenerate this file: `php tests/bin/postman-inventory.php --output=docs/postman-parity.md`';
$md[] = '';
$md[] = 'Legend: **Parity** = same endpoint and verb, usable for the Postman scenario; **Partial** = same endpoint with different query/body/helpers or bundled Postman variants; **OutOfScope** = intentionally not implemented; **Missing** = gap.';
$md[] = '';
$md[] = '| # | Postman (folder / name) | Method | Path (after `{{Platform}}`) | Package request | FQCN | Status | Integration test |';
$md[] = '|---:|---|---|---|---|---|---|---|';

$missing = 0;
foreach ($rows as $i => $row) {
    $n = $i + 1;
    $c = classify($row);
    if ($c['status'] === 'Missing') {
        $missing++;
    }
    $pathCol = str_replace('|', '\\|', $row['tail'] === '' ? '_(token / external URL)_' : $row['tail']);
    $folderCol = str_replace('|', '\\|', $row['folder']);
    $note = $c['note'] !== '' ? ' '.$c['note'] : '';
    $statusCol = $c['status'].$note;
    $fqcn = $c['class'];
    $short = shortClass($fqcn);
    $testPath = integrationTestRelativePath($fqcn, $packageRoot);
    $md[] = sprintf(
        '| %d | %s | %s | %s | `%s` | `%s` | %s | `%s` |',
        $n,
        $folderCol,
        strtoupper($row['method']),
        $pathCol,
        $short,
        $fqcn,
        str_replace('|', '\\|', $statusCol),
        $testPath
    );
}

$md[] = '';
$md[] = '## Package-only requests (not in this Postman export)';
$md[] = '';
$md[] = 'Examples: `GetTextshot`, `GetDocumentPreviewRequest`, `GetFieldsRequest` — useful DocuWare APIs present in the package without a matching leaf in the 81-request collection.';
$md[] = '';

if ($scanRequests) {
    $md[] = '## Scan: `src/Requests` (method + first `return` in `resolveEndpoint`)';
    $md[] = '';
    $md[] = '| FQCN | Method | Endpoint (snippet) |';
    $md[] = '|---|---|---|';
    foreach (scanRequestPhpFiles($packageRoot.'/src/Requests') as $info) {
        $md[] = sprintf(
            '| `%s` | %s | `%s` |',
            $info['class'],
            $info['method'],
            str_replace('|', '\\|', substr($info['endpoint_line'], 0, 120))
        );
    }
    $md[] = '';
}

$markdown = implode("\n", $md)."\n";

if ($outputPath !== null) {
    $fullOut = $packageRoot.'/'.ltrim($outputPath, '/');
    file_put_contents($fullOut, $markdown);
}

if ($outputPath === null) {
    echo $markdown;
}

if ($check && $missing > 0) {
    fwrite(STDERR, "postman-inventory: {$missing} row(s) classified as Missing.\n");

    exit(1);
}

exit(0);

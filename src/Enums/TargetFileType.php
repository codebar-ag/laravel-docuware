<?php

namespace CodebarAg\DocuWare\Enums;

/**
 * Target file type for downloading a document via the FileDownload endpoint.
 *
 * - AUTO: DocuWare decides the best representation (default).
 * - PDF: always render/convert to PDF.
 * - ORIGINAL: the originally stored file(s).
 */
enum TargetFileType: string
{
    case AUTO = 'Auto';
    case PDF = 'PDF';
    case ORIGINAL = 'Original';
}

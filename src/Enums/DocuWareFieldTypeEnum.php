<?php

namespace CodebarAg\DocuWare\Enums;

enum DocuWareFieldTypeEnum: string
{
    case STRING = 'String';
    case INT = 'Int';
    case DECIMAL = 'Decimal';
    case DATE = 'Date';
    case DATETIME = 'DateTime';
    case TABLE = 'Table';
}

<?php

namespace CodebarAg\DocuWare\Enums;

enum DialogType: string
{
    case SEARCH = 'Search';
    case STORE = 'Store';
    case RESULT = 'Result';
    case INDEX = 'Index';
    case LIST = 'List';
    case FOLDERS = 'Folders';
}

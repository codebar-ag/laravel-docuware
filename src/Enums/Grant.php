<?php

namespace CodebarAg\DocuWare\Enums;

/**
 * The OAuth grant an instance authenticates with.
 */
enum Grant: string
{
    case Credentials = 'credentials';
    case TrustedUser = 'trusted_user';
    case Token = 'token';
}

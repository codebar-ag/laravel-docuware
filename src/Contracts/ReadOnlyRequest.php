<?php

namespace CodebarAg\DocuWare\Contracts;

/**
 * Marks a request as semantically read-only despite a non-idempotent HTTP verb.
 * DocuWare models several pure queries as POST (dialog-expression search, select
 * lists, trash-bin queries); implementing this contract opts them into the same
 * connection-error and 5xx retry policy as idempotent verbs.
 *
 * Only implement this on requests with no observable side effects — replaying
 * the request against the server any number of times must be safe.
 */
interface ReadOnlyRequest {}

<?php

namespace CodebarAg\DocuWare\Exceptions;

/**
 * A 4xx/5xx response that isn't auth, rate-limit, or validation. Concrete subclasses cover the
 * common statuses ({@see BadRequestException}, {@see ForbiddenException}, {@see NotFoundException},
 * {@see ConflictException}, {@see MethodNotAllowedException}); other statuses throw this directly.
 */
class RequestException extends DocuWareException {}

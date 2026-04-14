<?php

namespace App\Common\Exception;

use RuntimeException;
use Throwable;

/**
 * Thrown by the Service layer for predictable, user-facing business errors
 * (e.g. "kredit tidak cukup", "jadwal sudah penuh"). The global handler in
 * bootstrap/app.php converts these into ApiResponse::error() instead of 500s.
 */
class BusinessException extends RuntimeException
{
    public function __construct(
        string $message,
        protected int $statusCode = 400,
        protected mixed $errors = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrors(): mixed
    {
        return $this->errors;
    }
}

<?php

namespace App\AppCore\CustomExceptions;

use Exception;
use Throwable;

class CustomException extends Exception
{
    protected array $errors;

    public function __construct(?string $message = "", ?int $code = 500, Throwable $previous = null, array $errors = [])
    {
        $this->errors = $errors;
        parent::__construct($message, $code, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
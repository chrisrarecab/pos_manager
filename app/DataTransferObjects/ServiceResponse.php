<?php

namespace App\DataTransferObjects;

class ServiceResponse
{
    public function __construct(
        public bool $success,
        public mixed $values = null,
        public string $message,
        public mixed $error = null,
        public int $statusCode = 200,
    ) {}

    
    public static function success(string $message = '', mixed $values = null, int $statusCode = 200): self
    {
        return new self(true, $values, $message, null, $statusCode);
    } 

    public static function failure(string $message = '', mixed $error = null, int $statusCode = 500): self
    {
        return new self(false, null, $message, $error ?? [], $statusCode);
    }
}


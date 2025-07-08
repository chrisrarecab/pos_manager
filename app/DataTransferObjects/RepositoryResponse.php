<?php

namespace App\DataTransferObjects;

class RepositoryResponse
{
    public function __construct(
        public bool $success,
        public string $message,
        public mixed $error = null,
        public int $code = 200,
        public mixed $data = null,
    ) {}
}


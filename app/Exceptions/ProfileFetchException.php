<?php

namespace App\Exceptions;

use Exception;

class ProfileFetchException extends Exception
{
    public function __construct(
        public bool $retriable,
        string $message
    ) {
        parent::__construct($message);
    }
}

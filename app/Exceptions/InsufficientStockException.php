<?php

namespace App\Exceptions;

use RuntimeException;

class InsufficientStockException extends RuntimeException
{
    public function __construct(string $message = 'Insufficient vehicle stock for the selected dates.')
    {
        parent::__construct($message);
    }
}

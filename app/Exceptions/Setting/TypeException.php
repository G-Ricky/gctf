<?php

namespace App\Exceptions\Setting;

use Exception;
use Throwable;

abstract class TypeException extends Exception
{
    public function __construct(string $message = "", int $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

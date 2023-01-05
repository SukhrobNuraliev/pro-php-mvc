<?php

namespace App\Exceptions;

class Handler extends \Framework\Support\ExceptionHandler
{
    public function showThrowable(\Throwable $throwable)
    {
        // add in some reporting...
        return parent::showThrowable($throwable);
    }
}
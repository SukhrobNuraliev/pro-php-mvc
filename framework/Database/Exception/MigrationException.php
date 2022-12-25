<?php

namespace Framework\Database\Exception;

class MigrationException extends \Exception
{

    /**
     * @param string $string
     */
    public function __construct(string $string)
    {
    }
}
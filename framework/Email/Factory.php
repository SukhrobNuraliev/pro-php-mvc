<?php

namespace Framework\Email;

use Closure;
use Framework\Support\DriverFactory;

class Factory implements DriverFactory
{

    public function addDriver(string $alias, Closure $driver): static
    {
        // TODO: Implement addDriver() method.
    }

    public function connect(array $config): mixed
    {
        // TODO: Implement connect() method.
    }
}
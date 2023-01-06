<?php

namespace Framework\Provider;

use Framework\App;
use Framework\Session\Driver\NativeDriver;
use Framework\Session\Factory;
use Framework\Support\DriverFactory;

class SessionProvider
{
    protected function name(): string
    {
        return 'session';
    }

    protected function factory(): DriverFactory
    {
        return new Factory();
    }

    protected function drivers(): array
    {
        return [
            'native' => function ($config) {
                return new NativeDriver($config);
            },
        ];
    }
}
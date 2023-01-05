<?php

namespace Framework\Provider;

use Framework\Queue\Driver\DatabaseDriver;
use Framework\Queue\Factory;
use Framework\Support\DriverFactory;
use Framework\Support\DriverProvider;

class QueueProvider extends DriverProvider
{

    protected function name(): string
    {
        return 'queue';
    }

    protected function factory(): DriverFactory
    {
        return new Factory();
    }

    protected function drivers(): array
    {
        return [
            'database' => function ($config) {
                return new DatabaseDriver($config);
            },
        ];
    }
}
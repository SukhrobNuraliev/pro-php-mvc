<?php

namespace Framework\Provider;

use Framework\App;
use Framework\Filesystem\Driver\LocalDriver;
use Framework\Filesystem\Factory;
use Framework\Support\DriverProvider;

class FilesystemProvider extends DriverProvider
{
    protected function name(): string
    {
        return 'filesystem';
    }

    protected function factory(): Factory
    {
        return new Factory();
    }

    protected function drivers(): array
    {
        return [
            'local' => function ($config) {
                return new LocalDriver($config);
            },
        ];
    }
}
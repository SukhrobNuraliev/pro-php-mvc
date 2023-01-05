<?php

namespace Framework\Provider;

use Framework\App;
use Framework\Cache\Driver\FileDriver;
use Framework\Cache\Driver\MemcacheDriver;
use Framework\Cache\Driver\MemoryDriver;
use Framework\Cache\Factory;
use Framework\Support\DriverFactory;
use Framework\Support\DriverProvider;

class CacheProvider extends DriverProvider
{
    protected function name(): string
    {
        return 'cache';
    }

    protected function factory(): Factory
    {
        return new Factory();
    }

    protected function drivers(): array
    {
        return [
            'file' => function ($config) {
                return new FileDriver($config);
            },
            'memcache' => function ($config) {
                return new MemcacheDriver($config);
            },
            'memory' => function ($config) {
                return new MemoryDriver($config);
            },
        ];
    }

}
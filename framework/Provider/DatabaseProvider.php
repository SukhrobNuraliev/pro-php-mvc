<?php

namespace Framework\Provider;

use Framework\App;
use Framework\Database\Connection\MysqlConnection;
use Framework\Database\Connection\SqliteConnection;
use Framework\Database\Factory;
use Framework\Support\DriverFactory;
use Framework\Support\DriverProvider;

class DatabaseProvider extends DriverProvider
{
    protected function name(): string
    {
        return 'database';
    }

    protected function factory(): Factory
    {
        return new Factory();
    }

    protected function drivers(): array
    {
        return [
            'sqlite' => function ($config) {
                return new SqliteConnection($config);
            },
            'mysql' => function ($config) {
                return new MysqlConnection($config);
            },
        ];
    }
}
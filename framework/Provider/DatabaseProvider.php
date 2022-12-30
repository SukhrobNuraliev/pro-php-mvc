<?php

namespace Framework\Provider;

use Framework\App;
use Framework\Database\Connection\MysqlConnection;
use Framework\Database\Connection\SqliteConnection;
use Framework\Database\Factory;

class DatabaseProvider
{
    public function bind(App $app): void
    {
        $app->bind('database', function ($app) {
            $factory = new Factory();
            $this->addMysqlConnector($factory);
            $this->addSqliteConnector($factory);

            $config = config('database');

            return $factory->connect($config[$config['default']]);
        });
    }

    private function addMysqlConnector($factory): void
    {
        $factory->addConnector('sqlite', function ($config) {
            return new SqliteConnection($config);
        });
    }

    private function addSqliteConnector($factory): void
    {
        $factory->addConnector('mysql', function ($config) {
            return new MysqlConnection($config);
        });
    }
}
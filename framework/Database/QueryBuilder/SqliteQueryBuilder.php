<?php

namespace Framework\Database\QueryBuilder;

use Framework\Database\Connection\Connection;
use Framework\Database\Connection\SqliteConnection;

class SqliteQueryBuilder extends QueryBuilder
{
    protected SqliteConnection $connection;

    public function __construct(SqliteConnection $connection)
    {
        $this->connection = $connection;
    }

    public function connection(): Connection
    {
        return $this->connection;
    }
}
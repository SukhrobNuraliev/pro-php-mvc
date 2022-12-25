<?php

namespace Framework\Database\Connection;

use Framework\Database\QueryBuilder\QueryBuilder;
use Framework\Database\QueryBuilder\SqliteQueryBuilder;
use InvalidArgumentException;
use PDO;

class SqliteConnection extends Connection
{
    private Pdo $pdo;

    public function __construct(array $config)
    {
        ['path' => $path] = $config;
        if (empty($path)) {
            throw new InvalidArgumentException('Connection incorrectly configured');
        }
        $this->pdo = new Pdo("sqlite:{$path}");
    }

    public function pdo(): Pdo
    {
        return $this->pdo;
    }

    public function query(): SqliteQueryBuilder
    {
        return new SqliteQueryBuilder($this);
    }
}
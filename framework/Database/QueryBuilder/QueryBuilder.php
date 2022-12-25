<?php

namespace Framework\Database\QueryBuilder;

use Framework\Database\Connection\Connection;
use Framework\Database\Exception\QueryException;
use Framework\Database\Model;
use PDO;
use PDOStatement;

abstract class QueryBuilder
{
    protected string $type;
    protected array $columns;
    protected string $table;
    protected int $limit;
    protected int $offset;
    protected array $values;
    protected array $wheres = [];

    /**
     * Get the underlying Connection instance for this query
     */
    abstract public function connection(): Connection;

    public static function __callStatic(string $method, array $parameters = []): mixed
    {
        return static::query()->$method(...$parameters);
    }

    /**
     * Fetch all rows matching the current query
     * @throws QueryException
     */
    public function all(): array
    {
        if (!isset($this->type)) {
            $this->select();
        }

        $statement = $this->prepare();
        $statement->execute($this->getWhereValues());
        return $statement->fetchAll(Pdo::FETCH_ASSOC);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public static function query(): mixed
    {
        /**
         * @var Model $model
         */
        $model = new static();
        return $model->getConnection()->query()
            ->from($model->getTable());
    }

    public function getLastInsertId(): string
    {
        return $this->connection->pdo()->lastInsertId();
    }

    /**
     * Store where clause data for later queries
     */
    public function where(string $column, mixed $comparator, mixed $value = null): static
    {
        if (is_null($value) && !is_null($comparator)) {
            $this->wheres[] = [$column, '=', $comparator];
        } else {
            $this->wheres[] = [$column, $comparator, $value];
        }

        return $this;
    }

    protected function getWhereValues(): array
    {
        $values = [];
        if (count($this->wheres) === 0) {
            return $values;
        }
        foreach ($this->wheres as $where) {
            $values[$where[0]] = $where[2];
        }
        return $values;
    }

    /**
     * Prepare a query against a particular connection
     * @throws QueryException
     */
    public
    function prepare(): PdoStatement
    {
        $query = '';

        if ($this->type === 'select') {
            $query = $this->compileSelect($query);
            $query = $this->compileWheres($query);
            $query = $this->compileLimit($query);
        }
        if ($this->type === 'insert') {
            $query = $this->compileInsert($query);
        }
        if ($this->type === 'update') {
            $query = $this->compileUpdate($query);
            $query = $this->compileWheres($query);
        }
        if ($this->type === 'delete') {
            $query = $this->compileDelete($query);
            $query = $this->compileWheres($query);
        }
        if (empty($query)) {
            throw new QueryException('Unrecognised query type');
        }
        return $this->connection->pdo()->prepare($query);
    }

    protected function compileDelete(string $query): string
    {
        $query .= " DELETE FROM {$this->table}";
        return $query;
    }

    /**
     * @throws QueryException
     */
    public function delete(): int
    {
        $this->type = 'delete';
        $statement = $this->prepare();
        return $statement->execute($this->getWhereValues());
    }

    protected function compileUpdate(string $query): string
    {
        $joinedColumns = '';
        foreach ($this->columns as $i => $column) {
            if ($i > 0) {
                $joinedColumns .= ', ';
            }
            $joinedColumns = " {$column} = :{$column}";
        }
        $query .= " UPDATE {$this->table} SET {$joinedColumns}";
        return $query;
    }

    /**
     * @throws QueryException
     */
    public function update(array $columns, array $values): int
    {
        $this->type = 'update';
        $this->columns = $columns;
        $this->values = $values;
        $statement = $this->prepare();
        return $statement->execute($this->getWhereValues() + $values);
    }

    protected function compileWheres(string $query): string
    {
        if (count($this->wheres) === 0) {
            return $query;
        }
        $query .= ' WHERE';
        foreach ($this->wheres as $i => $where) {
            if ($i > 0) {
                $query .= ', ';
            }
            [$column, $comparator, $value] = $where;
            $query .= " {$column} {$comparator} :{$column}";
        }
        return $query;
    }

    protected function compileInsert(string $query): string
    {
        $joinedColumns = join(', ', $this->columns);
        $joinedPlaceholders = join(', ', array_map(fn($column) => ":{$column}", $this->columns));
        $query .= " INSERT INTO {$this->table} ({$joinedColumns}) VALUES ({$joinedPlaceholders})";
        return $query;
    }

    /**
     * Add select clause to the query
     */
    protected function compileSelect(string $query): string
    {
        $query .= "SELECT {$this->columns} FROM {$this->table}";
        return $query;
    }

    /**
     * Add limit and offset clauses to the query
     */
    protected function compileLimit(string $query): string
    {
        if ($this->limit) {
            $query .= " LIMIT {$this->limit}";
        }
        if ($this->offset) {
            $query .= " OFFSET {$this->offset}";
        }
        return $query;
    }

    /**
     * Fetch the first row matching the current query
     * @throws QueryException
     */
    public function first(): ?array
    {
        if (!isset($this->type)) {
            $this->select();
        }

        $statement = $this->take(1)->prepare();
        $statement->execute($this->getWhereValues());
        $result = $statement->fetchAll(Pdo::FETCH_ASSOC);

        if (count($result) === 1) {
            return $result[0];
        }
        return null;
    }

    /**
     * Limit a set of query results so that it's possible
     * to fetch a single or limited batch of rows
     */
    public function take(int $limit, int $offset = 0): static
    {
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }

    /**
     * Indicate which table the query is targeting
     */
    public function from(string $table): static
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Indicate the query type is a "select" and remember
     * which fields should be returned by the query
     */
    public function select(string $columns = '*'): static
    {
        $this->type = 'select';
        $this->columns[] = $columns;
        return $this;
    }

    /**
     * @throws QueryException
     */
    public function insert(array $columns, array $values): int
    {
        $this->type = 'insert';
        $this->columns = $columns;
        $this->values = $values;
        $statement = $this->prepare();
        return $statement->execute($values);
    }
}
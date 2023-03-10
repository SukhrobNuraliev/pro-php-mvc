<?php

namespace Framework\Database;

use Exception;
use Framework\Database\Connection\Connection;
use Framework\Database\Connection\MysqlConnection;
use Framework\Database\Connection\SqliteConnection;
use Framework\Database\Exception\QueryException;
use Framework\Database\QueryBuilder\QueryBuilder;
use ReflectionClass;

abstract class Model
{
    protected Connection $connection;
    protected string $table;
    protected array $attributes;
    protected array $dirty = [];

    public function __get(string $property): mixed
    {
        $getter = 'get' . ucfirst($property) . 'Attribute';

        $value = null;

        if (method_exists($this, $property)) {
            $relationship = $this->$property();
            $method = $relationship->method;
            $value = $relationship->$method();
        }
        if (method_exists($this, $getter)) {
            return $this->$getter($this->attributes[$property] ?? null);
        }
        if (isset($this->attributes[$property])) {
            $value = $this->attributes[$property];
        }
        if (isset($this->casts[$property]) && is_callable($this->casts[$property])) {
            $value = $this->casts[$property]($value);
        }
        return $value;
    }

    public function __set(string $property, $value)
    {
        $setter = 'set' . ucfirst($property) . 'Attribute';
        $this->dirty[] = $property;

        if (method_exists($this, $setter)) {
            $this->attributes[$property] = $this->$setter($value);
        }
        $this->attributes[$property] = $value;
    }

    public function save(): static
    {
        $values = [];
        foreach ($this->dirty as $dirty) {
            $values[$dirty] = $this->attributes[$dirty];
        }
        $data = [array_keys($values), $values];
        $query = static::query();
        if (isset($this->attributes['id'])) {
            $query
                ->where('id', $this->attributes['id'])
                ->update(...$data);
            return $this;
        }
        $query->insert(...$data);
        $this->attributes['id'] = $query->getLastInsertId();
        return $this;
    }

    public static function with(array $attributes = []): static
    {
        $model = new static();
        $model->attributes = $attributes;
        return $model;
    }

    /**
     * @throws Exception
     */
    public static function query(): ModelCollector|QueryBuilder
    {
        $model = new static();
        $query = $model->getConnection()->query();
        return (new ModelCollector($query, static::class))
            ->from($model->getTable());
    }

    public static function find(int $id): static
    {
        return static::where('id', $id)->first();
    }

    public function setConnection(Connection $connection): static
    {
        $this->connection = $connection;
        return $this;
    }

    public function getConnection(): Connection
    {
        if (!isset($this->connection)) {
            $this->connection = app('database');
        }
        return $this->connection;
    }

    public function setTable(string $table): static
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function getTable(): string
    {
        if (!isset($this->table)) {
            $reflector = new ReflectionClass(static::class);
            foreach ($reflector->getAttributes() as $attribute) {
                if ($attribute->getName() == TableName::class) {
                    return $attribute->getArguments()[0];
                }
            }

            throw new Exception('$table is not set and getTable is not defined');
        }
        return $this->table;
    }

    /**
     * @throws Exception
     */
    public function delete(): static
    {
        if (isset($this->attributes['id'])) {
            static::query()
                ->where('id', $this->attributes['id'])
                ->delete();
        }
        return $this;
    }

    public function hasOne(string $class, string $foreignKey, string $primaryKey = 'id'): Relationship
    {
        $model = new $class;
        $query = $class::query()->from($model->getTable())->where($foreignKey, $this->attributes['id']);
        return new Relationship($query, 'first');
    }

    /**
     * @throws Exception
     */
    public function hasMany(string $class, string $foreignKey, string $primaryKey = 'id'): mixed
    {
        $model = new $class;
        $query = $class::query()->from($model->getTable())->where($foreignKey, $this->attributes['id']);
        return new Relationship($query, 'all');
    }

    public function belongsTo(string $class, string $foreignKey, string $primaryKey = 'id'): mixed
    {
        $model = new $class;
        $query = $class::query()->from($model->getTable())->where($primaryKey, $this->attributes[$foreignKey]);
        return new Relationship($query, 'first');
    }
}

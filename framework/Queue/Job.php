<?php

namespace Framework\Queue;

class Job extends \Framework\Database\Model
{
    private mixed $closure;
    private mixed $params;

    public function getTable(): string
    {
        return config('queue.database.table');
    }

    public function run(): mixed
    {
        $closure = unserialize($this->closure);
        $params = unserialize($this->params);
        return $closure(...$params);
    }
}
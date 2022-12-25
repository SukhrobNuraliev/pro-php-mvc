<?php

namespace Framework\Database;

class Relationship
{
    public ModelCollector $collector;
    public string $method;

    public function __construct(ModelCollector $collector, string $method)
    {
        $this->collector = $collector;
        $this->method = $method;
    }

    public function __invoke(array $parameters = []): mixed
    {
        return $this->collector->$this->method(...$parameters);
    }

    public function __call(string $method, array $parameters = []): mixed
    {
        return $this->collector->$method(...$parameters);
    }
}
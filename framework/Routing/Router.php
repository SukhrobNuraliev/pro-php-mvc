<?php

namespace Framework\Routing;

class Router
{
    protected array $routes = [];

    public function add(string $method, string $path, callable $handler): Route
    {
        $route = $this->routes[] = new Route($method, $path, $handler);
        return $route;
    }
}

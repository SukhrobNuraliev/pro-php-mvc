<?php

namespace Framework\Routing;

use Exception;
use Throwable;

class Router
{
    protected array $routes = [];
    protected array $errorHandlers = [];
    protected Route $current;

    public function add(string $method, string $path, callable $handler): Route
    {
        $route = $this->routes[] = new Route($method, $path, $handler);
        return $route;
    }

    public function dispatch()
    {
        $paths = $this->paths();

        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestPath = $_SERVER['REQUEST_URI'] ?? '/';

        // this looks through the defined routes and returns
        // the first that matches the requested method and path
        $matching = $this->match($requestMethod, $requestPath);

        if ($matching) {
            try {
                // this action could throw and exception
                // so we catch it and display the global error
                // page that we will define in the routes file
                return $matching->dispatch();
            } catch (Throwable $e) {
                return $this->dispatchError();
            }
        }

        // if the path is defined for a different method
        // we can show a unique error page for it
        if (in_array($requestPath, $paths)) {
            return $this->dispatchNotAllowed();
        }
        return $this->dispatchNotFound();
    }

    private function paths(): array
    {
        $paths = [];
        foreach ($this->routes as $route) {
            $paths[] = $route->path();
        }
        return $paths;
    }

    private function match(string $method, string $path): ?Route
    {
        foreach ($this->routes as $route) {
            if ($route->matches($method, $path)) {
                return $route;
            }
        }
        return null;
    }

    public function errorHandler(int $code, callable $handler): void
    {
        $this->errorHandlers[$code] = $handler;
    }

    public function dispatchNotAllowed()
    {
        $this->errorHandlers[400] ??= fn() => "not allowed";
        return $this->errorHandlers[400]();
    }

    public function dispatchNotFound()
    {
        $this->errorHandlers[404] ??= fn() => "not found";
        return $this->errorHandlers[404]();
    }

    public function dispatchError()
    {
        $this->errorHandlers[500] ??= fn() => "server error";
        return $this->errorHandlers[500]();
    }

    public function redirect($path)
    {
        header(
            "Location: {$path}", $replace = true, $code = 301
        );
        exit;
    }

    public function current(): ?Route
    {
        return $this->current;
    }

    /**
     * @throws Exception
     */
    public function route(
        string $name,
        array  $parameters = [],
    ): string
    {
        foreach ($this->routes as $route) {
            if ($route->name() === $name) {
                $finds = [];
                $replaces = [];
                foreach ($parameters as $key => $value) {
                    // one set for required parameters
                    $finds[] = "{{$key}}";
                    $replaces[] = $value;
                    // ...and another for optional parameters
                    $finds[] = "{{$key}?}";
                    $replaces[] = $value;
                }
                $path = $route->path();
                $path = str_replace($finds, $replaces, $path);
                // remove any optional parameters not provided
                $path = preg_replace('#{[^}]+}#', '', $path);
                // we should think about warning if a required
                // parameter hasn't been provided...
                return $path;
            }
        }
        throw new Exception('no route with that name');
    }

}
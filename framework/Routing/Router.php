<?php

namespace Framework\Routing;

use Exception;
use Framework\Validation\ValidationException;
use Throwable;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class Router
{
    protected array $routes = [];
    protected array $errorHandlers = [];
    protected Route $current;

    public function add(string $method, string $path, $handler): Route
    {
        $route = $this->routes[] = new Route($method, $path, $handler);
        return $route;
    }

    /**
     * @throws Throwable
     */
    public function dispatch()
    {
        $paths = $this->paths();

        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestPath = $_SERVER['REQUEST_URI'] ?? '/';

        $matching = $this->match($requestMethod, $requestPath);

        if ($matching) {
            $this->current = $matching;
            try {
                return $matching->dispatch();
            } catch (Throwable $e) {
                if ($e instanceof ValidationException) {
                    $_SESSION['errors'] = $e->getErrors();
                    return redirect($_SERVER['HTTP_REFERER']);
                }

                if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'dev') {
                    $whoops = new Run();
                    $whoops->pushHandler(new PrettyPageHandler());
                    $whoops->register();
                    throw $e;
                }
                return $this->dispatchError();
            }
        }

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
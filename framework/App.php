<?php

namespace Framework;

use Dotenv\Dotenv;
use Framework\Http\Response;
use Framework\Routing\Router;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use Throwable;

class App extends Container
{
    private static $instance;

    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function prepare(): static
    {
        $basePath = $this->resolve('paths.base');
        $this->configure($basePath);
        $this->bindProviders($basePath);
        return $this;
    }

    /**
     * @throws Throwable
     */
    public function run()
    {
        return $this->dispatch($this->resolve('paths.base'));
        /*$basePath = $this->resolve('paths.base');

        $this->configure($basePath);
        $this->bindProviders($basePath);

        $this->dispatch($basePath);*/
    }

    private function configure(string $basePath)
    {
        $dotenv = Dotenv::createImmutable($basePath);
        $dotenv->load();
    }

    private function bindProviders(string $basePath)
    {
        $providers = require "{$basePath}/config/providers.php";
        foreach ($providers as $provider) {
            $instance = new $provider;
            if (method_exists($instance, 'bind')) {
                $instance->bind($this);
            }
        }
    }

    /**
     * @throws Throwable
     */
    private function dispatch(string $basePath)
    {
        $router = new Router();
        $this->bind(Router::class, fn() => $router);
        $routes = require "{$basePath}/app/routes.php";
        $routes($router);
        $response = $router->dispatch();

        if (!$response instanceof Response) {
            $response = $this->resolve('response')->content($response);
        }
        return $response;
    }


}
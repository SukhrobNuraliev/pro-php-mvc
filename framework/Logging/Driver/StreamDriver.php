<?php

namespace Framework\Logging\Driver;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class StreamDriver implements Driver
{
    private array $config;
    private Logger $logger;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function info(string $message): static
    {
        $this->logger()->info($message);
        return $this;
    }

    public function warning(string $message): static
    {
        $this->logger()->warning($message);
        return $this;
    }

    public function error(string $message): static
    {
        $this->logger()->error($message);
        return $this;
    }

    private function logger(): Logger
    {
        if (!isset($this->logger)) {
            $this->logger = new Logger($this->config['name']);
            $this->logger->pushHandler(new StreamHandler($this->config['path'], $this->config['minimum']));
        }
        return $this->logger;
    }
}
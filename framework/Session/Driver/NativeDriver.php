<?php

namespace Framework\Session\Driver;

class NativeDriver implements Driver
{
    private array $config = [];

    public function __construct(array $config)
    {
        $this->config = $config;
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        $prefix = $this->config['prefix'];
        return isset($_SESSION["{$prefix}{$key}"]);
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $prefix = $this->config['prefix'];

        if (isset($_SESSION["{$prefix}{$key}"])) {
            return $_SESSION["{$prefix}{$key}"];
        }
        return $default;
    }

    /**
     * @inheritDoc
     */
    public function put(string $key, mixed $value): static
    {
        $prefix = $this->config['prefix'];
        $_SESSION["{$prefix}{$key}"] = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function forget(string $key): static
    {
        $prefix = $this->config['prefix'];
        unset($_SESSION["{$prefix}{$key}"]);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function flush(): static
    {
        foreach (array_keys($_SESSION) as $key) {
            $prefix = config('session.native.prefix');
            if (str_starts_with($key, $prefix)) {
                unset($_SESSION[$key]);
            }
        }
        return $this;
    }
}
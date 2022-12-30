<?php

namespace Framework\Cache\Driver;

use Memcached;

class MemcacheDriver implements Driver
{
    private array $config = [];
    private Memcached $memcache;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->memcache = new Memcached();
        $this->memcache->addServer($config['host'], $config['port']);
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return $this->memcache->get($key) !== false;
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if ($value = $this->memcache->get($key)) {
            return $value;
        }
        return $default;
    }

    /**
     * @inheritDoc
     */
    public function put(string $key, mixed $value, int $seconds = null): static
    {
        if (!is_int($seconds)) {
            $seconds = (int)$this->config['seconds'];
        }
        $this->memcache->set($key, $value, time() + $seconds);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function forget(string $key): static
    {
        $this->memcache->delete($key);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function flush(): static
    {
        $this->memcache->flush();
        return $this;
    }
}
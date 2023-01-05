<?php

namespace Framework\Filesystem\Driver;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;

class LocalDriver extends Driver
{
    private $config;

    protected function connect(array $config): Filesystem
    {
        $adapter = new LocalFilesystemAdapter($this->config['path']);
        $this->filesystem = new Filesystem($adapter);
    }
}
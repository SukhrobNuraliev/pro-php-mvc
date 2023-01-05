<?php

namespace Framework\Provider;

use Framework\App;
use Framework\Session\Driver\NativeDriver;
use Framework\Session\Factory;

class SessionProvider
{
    public function bind(App $app): void
    {
        $app->bind('session', function ($app) {
            $factory = new Factory();
            $this->addNativeDriver($factory);
            $config = config('session');
            return $factory->connect($config[$config['default']]);
        });
    }

    private function addNativeDriver(Factory $factory): void
    {
        $factory->addDriver('native', function ($config) {
            return new NativeDriver($config);
        });
    }
}
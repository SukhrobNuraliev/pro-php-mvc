<?php

namespace Framework\Provider;

use Framework\Email\Driver\PostmarkDriver;
use Framework\Email\Factory;
use Framework\Support\DriverFactory;
use Framework\Support\DriverProvider;

class EmailProvider extends DriverProvider
{

    protected function name(): string
    {
        return 'email';
    }

    protected function factory(): DriverFactory
    {
        return new Factory();
    }

    protected function drivers(): array
    {
        return [
            'postmark' => function ($config) {
                return new PostmarkDriver($config);
            },
        ];
    }
}
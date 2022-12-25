<?php

use Framework\Database\Connection\MysqlConnection;
use Framework\Database\Connection\SqliteConnection;
use Framework\Database\Factory;
use Framework\View\View;

class ShowHomeController
{
    /**
     * @throws Exception
     */
    public function handle(): View
    {
        $factory = new Factory();
        $factory->addConnector('mysql', function ($config) {
            return new MysqlConnection($config);
        });
        $factory->addConnector('sqlite', function ($config) {
            return new SqliteConnection($config);
        });

        $config = require __DIR__ . '/../../../config/database.php';

        $connection = $factory->connect($config[$config['default']]);

        $product = $connection
            ->query()
            ->select()
            ->from('products')
            ->first();

        return view('home', [
            'number' => 42,
            'featured' => $product,
        ]);
    }
}

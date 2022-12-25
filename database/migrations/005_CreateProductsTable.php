<?php

use Framework\Database\Connection\Connection;

class CreateProductsTable
{
    public function migrate(Connection $connection): void
    {
        $table = $connection->createTable('products');
        $table->id('id');
        $table->string('name');
        $table->text('description');
        $table->execute();
    }
}

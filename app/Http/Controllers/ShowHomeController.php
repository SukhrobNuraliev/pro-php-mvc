<?php

use App\Models\Product;
use Framework\Database\Connection\MysqlConnection;
use Framework\Database\Connection\SqliteConnection;
use Framework\Database\Factory;
use Framework\Routing\Router;
use Framework\View\View;

class ShowHomeController
{
    /**
     * @throws Exception
     */
    public function handle(Router $router): View
    {
        $products = Product::all();

        $productsWithRoutes = array_map(function ($product) {
            $product->route = $this->router->route('view-product', ['product' => $product->id]);
            return $product;
        }, $products);

        return view('home', [
            'products' => $productsWithRoutes,
        ]);
    }
}

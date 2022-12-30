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
        $cache = app('cache');
        $products = Product::all();

        $productsWithRoutes = array_map(function ($product) use ($cache, $router) {
            $key = "route-for-product-{$product->id}";

            if (!$cache->has($key)) {
                $cache->put($key, $router->route('view-product', ['product' => $product->id]));
            }
            $product->route = $cache->get($key);
            return $product;

        }, $products);

        return view('home', [
            'products' => $productsWithRoutes,
        ]);
    }
}

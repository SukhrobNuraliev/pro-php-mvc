<?php

namespace App\Http\Controllers\Products;

use Framework\Routing\Router;

class ListProductsController
{
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $parameters = $this->router->current()->parameters();
        $parameters['page'] ??= 1;
        $next = $this->router->route(
            'list-products', ['page' => $parameters['page'] + 1]
        );
        return view('products/list', [
            'parameters' => $parameters,
            'next' => $next,
        ]);
    }
}

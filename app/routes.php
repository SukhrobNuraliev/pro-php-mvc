<?php

use App\Http\Controllers\Products\ListProductsController;
use App\Http\Controllers\Products\ShowProductController;
use App\Http\Controllers\Services\ShowServiceController;
use Framework\Routing\Router;

return function (Router $router) {
    $router->add(
        'GET', '/',
        [ShowHomeController::class, 'handle']
    );
    $router->add(
        'GET', '/old-home',
        fn() => $router->redirect('/'),
    );
    $router->add(
        'GET', '/has-server-error',
        fn() => throw new Exception(),
    );
    $router->add(
        'GET', '/has-validation-error',
        fn() => $router->dispatchNotAllowed(),
    );
    $router->add(
        'GET', '/products/view/{product}',
        [new ShowProductController($router), 'handle'],
    );
    $router->add(
        'GET', '/services/view/{service?}',
        [new ShowServiceController($router), 'handle'],
    );
    $router->add(
        'GET', '/products/{page?}',
        [new ListProductsController($router), 'handle']
    )->name('product-list');
    $router->add(
        'GET', '/register',
        [new ShowRegisterFormController($router), 'handle'],
    )->name('show-register-form');
    $router->add(
        'POST', '/register',
        [new RegisterUserController($router), 'handle'],
    )->name('register-user');
};

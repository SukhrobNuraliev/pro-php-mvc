<?php

function redirectForeverTo($path)
{
    header("Location: {$path}", $replace = true, $code = 301);
    exit;
}

$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$requestPath = $_SERVER['REQUEST_URI'] ?? '/';

$routes = [
    'GET' => [
        '/' => fn() => print
            <<<HTML
                <!doctype html>
                <html lang="en">
                    <body>
                        hello world
                    </body>
                </html>
            HTML,
        '/old-home' => fn() => redirectForeverTo('/'),
        '/salam' => fn() => print 'salam',
        '/has-server-error' => fn() => throw new Exception(),
        '/has-validation-error' => fn() => abort(400),
    ],
    'POST' => [],
    'PATCH' => [],
    'PUT' => [],
    'DELETE' => [],
    'HEAD' => [],
    '404' => fn() => include(__DIR__ . '/includes/404.php'),
    '400' => fn() => include(__DIR__ . '/includes/400.php'),
    '500' => fn() => include(__DIR__ . '/includes/500.php'),
];

function abort($code): void
{
    global $routes;
    header('HTTP/1.1 500 Internal Server Error');
    $routes[$code]();
}

set_error_handler(function () {
    abort(500);
});
set_exception_handler(function () {
    abort(500);
});

$paths = array_merge(
    array_keys($routes['GET']),
    array_keys($routes['POST']),
    array_keys($routes['PATCH']),
    array_keys($routes['PUT']),
    array_keys($routes['DELETE']),
    array_keys($routes['HEAD']),
);

if (isset(
    $routes[$requestMethod],
    $routes[$requestMethod][$requestPath],
)) {
    $routes[$requestMethod][$requestPath]();
} else if (in_array($requestPath, $paths)) {
    $routes['400']();
} else {
    $routes['404']();
}
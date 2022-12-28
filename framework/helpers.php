<?php

use Framework\App;
use Framework\Validation\Manager;
use Framework\Validation\Rule\EmailRule;
use Framework\Validation\Rule\MinRule;
use Framework\Validation\Rule\RequiredRule;
use Framework\View;
use JetBrains\PhpStorm\NoReturn;

if (!function_exists('view')) {
    function view(string $template, array $data = []): View\View
    {
        return app()->resolve('view')->resolve($template, $data);
    }
}

if (!function_exists('response')) {
    function response()
    {
        return app('response');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url)
    {
        return response()->redirect($url);
    }
}

if (!function_exists('validate')) {

    app()->bind('validator', function ($app) {
        $manager = new Manager();
        // let's add the rules that come with the framework
        $manager->addRule('required', new RequiredRule());
        $manager->addRule('email', new EmailRule());
        $manager->addRule('min', new MinRule());

        return $manager;
    });

    function validate(array $data, array $rules, string $sessionName = 'errors')
    {
        return app('validator')->validate($data, $rules, $sessionName);
    }
}

if (!function_exists('csrf')) {
    /**
     * @throws Exception
     */
    function csrf(): string
    {
        $_SESSION['token'] = bin2hex(random_bytes(32));
        return $_SESSION['token'];
    }
}

if (!function_exists('secure')) {
    /**
     * @throws Exception
     */
    function secure(): void
    {
        if (!isset($_POST['csrf']) || !isset($_SESSION['token']) ||
            !hash_equals($_SESSION['token'], $_POST['csrf'])) {
            throw new Exception('CSRF token mismatch');
        }
    }
}

if (!function_exists('basePath')) {
    function basePath(string $newBasePath = null): ?string
    {
        // static $basePath;
        // if (!is_null($newBasePath)) {
        //     $basePath = $newBasePath;
        // }
        // return $basePath;
        return app('paths.base');
    }
}
if (!function_exists('app')) {
    function app(string $alias = null): mixed
    {
        if (is_null($alias)) {
            return App::getInstance();
        }
        return App::getInstance()->resolve($alias);
    }
}
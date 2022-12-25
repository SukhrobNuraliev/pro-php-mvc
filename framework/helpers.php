<?php

use Framework\View;
use JetBrains\PhpStorm\NoReturn;

if (!function_exists('view')) {
    /**
     * @throws Exception
     */
    function view(string $template, array $data = []): View\View
    {
        static $manager;

        if (!$manager) {
            $manager = new View\Manager();
            // let's add a path for our views folder
            // so the manager knows where to look for views
            $manager->addPath(__DIR__ . '/../resources/views');
            // we'll also start adding new engine classes
            // with their expected extensions to be able to pick
            // the appropriate engine for the template
            $manager->addEngine('basic.php', new View\Engine\BasicEngine());
            $manager->addEngine('php', new View\Engine\PhpEngine());
            $manager->addEngine('php', new View\Engine\PhpEngine());

            $manager->addMacro('escape', fn($value) => htmlspecialchars($value));
            $manager->addMacro('includes', fn(...$params) => print view(...$params));
        }

        return $manager->resolve($template, $data);
    }
}

if (!function_exists('redirect')) {
    #[NoReturn] function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
}

if (!function_exists('validate')) {
    function validate(array $data, array $rules)
    {
        static $manager;
        if (!$manager) {
            $manager = new Validation\Manager();
            // let's add the rules that come with the framework
            $manager->addRule('required', new Validation\Rule\RequiredRule());
            $manager->addRule('email', new Validation\Rule\EmailRule());
            $manager->addRule('min', new Validation\Rule\MinRule());
        }
        return $manager->validate($data, $rules);
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
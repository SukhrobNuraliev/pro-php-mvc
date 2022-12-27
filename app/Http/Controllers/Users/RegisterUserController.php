<?php

namespace App\Http\Controllers\Users;

use Exception;
use Framework\Routing\Router;
use JetBrains\PhpStorm\NoReturn;

class RegisterUserController
{
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @throws Exception
     */
    #[NoReturn] public function handle()
    {
        secure();

        $data = validate($_POST, [
            'name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:10'],
        ], 'register_errors');

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
        $user->save();

        // use $data to create a database record...
        $_SESSION['registered'] = true;

        return redirect($this->router->route('show-home-page'));
    }
}
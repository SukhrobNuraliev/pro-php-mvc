<?php

namespace App\Http\Controllers\Users;

use App\Models\User;
use Exception;
use JetBrains\PhpStorm\NoReturn;

class LogInUserController
{
    /**
     * @throws Exception
     */
    #[NoReturn] public function handle()
    {
        // check the csrf token...
        secure();
        $data = validate($_POST, [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:10'],
        ], 'login_errors');
        $user = User::where('email', $data['email'])->first();
        if ($user && password_verify($data['password'], $user->password)) {
            $_SESSION['user_id'] = $user->id;
        }
        return redirect($this->router->route('show-home-page'));
    }
}
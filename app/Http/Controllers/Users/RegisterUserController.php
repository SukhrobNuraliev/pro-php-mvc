<?php

use Framework\Routing\Router;

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
    public function handle()
    {
        secure();

        $data = validate($_POST, [
            'name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:10'],
        ]);
        // use $data to create a database record...
        $_SESSION['registered'] = true;

        return redirect($this->router->route('show-home-page'));
    }
}
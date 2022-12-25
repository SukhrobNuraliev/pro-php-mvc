<?php

use Framework\Routing\Router;

class ShowRegisterFormController
{
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @throws Exception
     */
    public function handle(): \Framework\View\View
    {
        return view('users/register', [
            'router' => $this->router,
        ]);
    }
}
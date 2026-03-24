<?php

namespace App\Controllers;

use App\Services\UserService;

class HomeController
{
    public function __construct(protected UserService $userService) {}

    public function index($id = null)
    {
        return view("pages.home", [
            "title" => "My App",
        ]);
    }

    public function dashboard($id = null)
    {
        $columnArray = [
            ['id', '=', 5]
        ];

        $user = $this->userService->getOne($columnArray);
        // print_r($user);
        // exit;

        return view("pages.home", [
            "name" => $user["fname"],
            "title" => $id ?? "Home",
        ]);
    }
}

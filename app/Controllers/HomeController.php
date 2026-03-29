<?php

namespace App\Controllers;

use App\Services\UserService;

class HomeController
{
    public function __construct(protected UserService $userService) {}

    public function index($slug = null)
    {
        return view("pages.home", [
            "title" => $slug ?? "Ziro",
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
            "title" => $id ?? "Dashboard",
        ]);
    }

    public function login()
    {
        return view(
            "pages.login",
            ["title" => "Login", "header" => "Login to your account"],
            'layouts/auth'
        );
    }
}

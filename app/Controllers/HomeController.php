<?php

namespace App\Controllers;

use App\Services\UserService;

class HomeController
{
    public function __construct(protected UserService $userService) {}

    public function index($id = null)
    {
        // $users = $this->userService->getAll();
        $user = $this->userService->getOne();

        return view("pages.home", [
            "name" => $user["name"],
            "title" => $id ?? "Home",
        ]);
    }
}

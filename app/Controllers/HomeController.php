<?php

namespace App\Controllers;

class HomeController
{
    public function index($slug = null)
    {
        return view("pages.home", [
            "title" => $slug ?? "Ziro",
        ]);
    }

    public function dashboard()
    {
        return view("pages.dashboard", [
            "title" => "Dashboard",
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

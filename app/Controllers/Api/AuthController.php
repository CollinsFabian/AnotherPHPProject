<?php

namespace App\Controllers\Api;

class AuthController
{
    public function login()
    {
        return json(["status" => "success", "message" => "You are logged-in"]);
    }

    public function logout()
    {
        return json(["status" => "success", "message" => "You logged-out"]);
    }
}

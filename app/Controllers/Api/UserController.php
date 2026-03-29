<?php

namespace App\Controllers\Api;

class UserController
{
    public function profile()
    {
        return json(["status" => "error", "message" => "Error Processing Request"]);
    }
}

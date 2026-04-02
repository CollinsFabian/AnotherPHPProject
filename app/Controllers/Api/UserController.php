<?php

namespace App\Controllers\Api;

use App\Entity\User;

class UserController
{
    public function profile()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : null;
        $user = $id !== null ? User::find($id) : null;

        if ($user === null) {
            return json([
                "status" => "error",
                "message" => "User not found",
            ], 404);
        }

        return json(["status" => "success", "data" => $user]);
    }
}

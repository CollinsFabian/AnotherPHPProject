<?php

namespace Ziro\Controllers\Api;

use Ziro\Entity\User;
use Ziro\System\Http\Request;

class UserController
{
    public function profile(Request $request)
    {
        $id = $request->input('id');
        $id = is_numeric($id) ? (int) $id : null;
        $user = $id !== null ? User::find($id) : null;

        if ($user === null) {
            return json([
                "status" => "error",
                "message" => "User not found",
            ], 404);
        }

        return json(['status' => 'success', 'data' => $user]);
    }
}

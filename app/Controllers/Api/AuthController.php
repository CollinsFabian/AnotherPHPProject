<?php

namespace Ziro\Controllers\Api;

use Ziro\System\Http\Request;

class AuthController
{
    public function login(Request $request)
    {
        return json([
            'status' => 'success',
            'message' => 'You are logged-in',
            'meta' => [
                'cors_enabled' => true,
                'content_type' => $request->header('Content-Type'),
            ],
        ]);
    }

    public function logout(Request $request)
    {
        return json([
            'status' => 'success',
            'message' => 'You logged-out',
            'request_method' => $request->method,
        ]);
    }
}

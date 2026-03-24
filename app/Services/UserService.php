<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getAll(): array
    {
        return [];
        return (new User())->getAll();
    }

    public function getOne($columnArray): array
    {
        return [];
        return (new User())->getOne($columnArray);
    }
}

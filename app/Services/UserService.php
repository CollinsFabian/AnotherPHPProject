<?php

namespace App\Services;

class UserService
{
    public function getAll(): array
    {
        return [
            ['id' => 1, 'name' => 'Urchmann'],
            ['id' => 2, 'name' => 'Zi Urch'],
        ];
    }

    public function getOne(): array
    {
        return ['name' => 'Urchmann'];
    }
}

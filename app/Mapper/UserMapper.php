<?php

namespace App\Mapper;

use ZQuery\Mapper\BaseMapper;
use App\Entity\User;

class UserMapper extends BaseMapper
{
    protected string $entityClass = User::class;
}

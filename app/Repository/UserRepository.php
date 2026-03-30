<?php

namespace App\Repository;

use ZQuery\Repository\BaseRepository;
use App\Mapper\UserMapper;

class UserRepository extends BaseRepository
{
    public function __construct(UserMapper $mapper)
    {
        parent::__construct($mapper);
    }
}

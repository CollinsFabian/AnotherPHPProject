<?php

namespace App\Entity;

use ZQuery\Entity\Attributes\Column;

class User extends Model
{
    protected static string $table = 'users';
    protected static string $primaryKey = 'id';

    #[Column('id')]
    public int $id;
    public string $fname;
    public string $othrnames;
}

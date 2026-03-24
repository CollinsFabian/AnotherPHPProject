<?php

namespace App\Models;

class User extends Model
{
    public static $id;
    public static $fname;
    public static $othrnames;
    // public static $id;

    public function getAll()
    {
        return $this->ZQuery->table("users")->select()->get();
    }

    public function getOne($columnArray)
    {
        $res = $this->ZQuery->table("enread_users")->select()->where($columnArray)->get();
        return array_merge(...$res);
    }
}

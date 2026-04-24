<?php

namespace Ziro\Entity;

use PDO;
use ZQuery\Query\Grammar\MysqlGrammar;
use ZQuery\Support\ConfigLoader;
use ZQuery\ZQuery;

class Model
{
    protected static ?ZQuery $zquery = null;

    public static function zquery(): ZQuery
    {
        if (self::$zquery instanceof ZQuery) {
            return self::$zquery;
        }

        $DB_PDO_DSN = ConfigLoader::get("DB_PDO_DSN");
        $DB_USER = ConfigLoader::get("DB_USER");
        $DB_PASSWORD = ConfigLoader::get("DB_PASSWORD");

        $config = [
            "prefix" => "",
            "engine" => "pdo",
            "pdo" => new PDO($DB_PDO_DSN, $DB_USER, $DB_PASSWORD),
            "grammar" => new MysqlGrammar(),
        ];

        self::$zquery = new ZQuery($config);
        return self::$zquery;
    }
}

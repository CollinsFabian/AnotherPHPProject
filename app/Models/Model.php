<?php

namespace App\Models;

use PDO;
use ZQuery\Query\Grammar\MysqlGrammar;
use ZQuery\Support\ConfigLoader;
use ZQuery\ZQuery;

class Model
{
    public ZQuery $ZQuery;

    public function __construct()
    {
        try {
            $DB_PDO_DSN = ConfigLoader::get("DB_PDO_DSN");
            $DB_USER = ConfigLoader::get("DB_USER");
            $DB_PASSWORD = ConfigLoader::get("DB_PASSWORD");

            $config = [
                "prefix" => "",
                "engine" => "pdo",
                "pdo" => new PDO($DB_PDO_DSN, $DB_USER, $DB_PASSWORD),
                "grammer" => new MysqlGrammar(),
            ];

            $this->ZQuery = new ZQuery($config);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

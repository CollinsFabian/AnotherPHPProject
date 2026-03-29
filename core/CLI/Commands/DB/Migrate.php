<?php

namespace Core\CLI\Commands\DB;

use Core\CLI\Command;
use Core\CLI\ConsoleMessages;
use mysqli;
use ZQuery\Support\ConfigLoader;
use ZQuery\ZQuery;

class Migrate extends Command
{
    use ConsoleMessages;

    public function run(array $args)
    {
        $this::brand();

        $DB_HOST = ConfigLoader::get("DB_HOST");
        $DB_USER = ConfigLoader::get("DB_USER");
        $DB_NAME = ConfigLoader::get("DB_NAME");
        $DB_PORT = ConfigLoader::get("DB_PORT");
        $DB_PASSWORD = ConfigLoader::get("DB_PASSWORD");

        $mysql = new mysqli($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME, $DB_PORT);
        $db = new ZQuery(["engine" => "mysql", "mysql" => $mysql]);

        $migrationPath = __DIR__ . "/../../../database/migrations/";
        $files = glob("{$migrationPath}.php");

        // get executed migrations
        $executed = [];
        $result = $db->table("migrations")->select(["migration"])->get();

        for ($i = 0; $i < count($result); $i++) $executed[] = $result['migration'];
        foreach ($files as $file) {
            $name = basename($file);

            if (in_array($name, $executed)) continue;

            echo "\033[32mRunning: {$name}\033[0m\n";

            $migration = require $file;
            $migration->up($db);

            $db->table("migrations")->insert(["migration" => $name])->executeInsert();
        }

        echo "\033[32mMigration complete\033[0m\n";
    }
}

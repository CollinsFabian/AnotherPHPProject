<?php

namespace Ziro\System\CLI\Commands\DB;

use Ziro\System\CLI\Command;
use Ziro\System\CLI\ConsoleMessages;
use ZQuery\Support\ConfigLoader;
use ZQuery\ZQuery;
use PDO;
use ZQuery\Query\Grammar\MysqlGrammar;
use ZQuery\Support\Environment;

class Migrate extends Command
{
    use ConsoleMessages;

    public function run(array $args)
    {
        $this::brand();

        Environment::load(base_path('.env'));
        $DB_PDO_DSN = ConfigLoader::get("DB_PDO_DSN");
        $DB_USER = ConfigLoader::get("DB_USER");
        $DB_PASSWORD = ConfigLoader::get("DB_PASSWORD");

        $db = new ZQuery([
            "engine" => "pdo",
            "pdo" => new PDO($DB_PDO_DSN, $DB_USER, $DB_PASSWORD),
            "grammar" => new MysqlGrammar(),
        ]);

        $migrationPath = base_path('database/migrations/');
        $files = glob("{$migrationPath}*.php");

        // ensure migrations table exists
        $db->getConnection()->execute(
            "CREATE TABLE IF NOT EXISTS `migrations` (
                `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `migration` VARCHAR(255) NOT NULL UNIQUE,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );

        // get executed migrations
        $executed = [];
        $result = $db->table("migrations")->select(["migration"])->get();

        for ($i = 0; $i < count($result); $i++) {
            $executed[] = $result[$i]['migration'];
        }
        foreach ($files as $file) {
            $name = basename($file);

            if (in_array($name, $executed)) continue;

            $this->normalM("Running: {$name}");

            $migration = require $file;
            $migration->up($db);

            $db->table("migrations")->insert(["migration" => $name])->executeInsert();
        }

        $this->successM("Migration complete");
    }
}

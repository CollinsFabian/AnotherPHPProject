<?php

use ZQuery\ZQuery;

return new class
{
    public function up(ZQuery $db)
    {
        $db->getConnection()->execute("
            CREATE TABLE users(
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100),
                email VARCHAR(150),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    public function down(ZQuery $db)
    {
        $db->getConnection()->execute("DROP TABLE users");
    }
};

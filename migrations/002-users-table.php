<?php

use App\Database;

return function(Database $db) {
    $db->exec(
        "CREATE TABLE koko_analytics_users (
              id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              email VARCHAR(255) NOT NULL,
              password VARCHAR(255) NOT NULL DEFAULT '',
              role ENUM ('viewer', 'admin') DEFAULT 'viewer',
              UNIQUE INDEX (email)
        ) ENGINE=INNODB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    );
};

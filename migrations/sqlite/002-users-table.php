<?php

use App\Database;

return function(Database $db) {
    $db->exec(
        "CREATE TABLE koko_analytics_users (
              id INTEGER PRIMARY KEY,
              email VARCHAR(255) NOT NULL,
              password VARCHAR(255) NOT NULL DEFAULT '',
              role VARCHAR(32) NOT NULL DEFAULT 'viewer',
              UNIQUE (email)
        )"
    );
};

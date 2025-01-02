<?php

use App\Database;

return function(Database $db) {
    $db->exec(
        "CREATE TABLE koko_analytics_domains (
            id INTEGER PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            UNIQUE (name)
        )"
    );
};

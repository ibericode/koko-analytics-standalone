<?php

use App\Database;

return function(Database $db) {
    $db->exec(
        "CREATE TABLE koko_analytics_domains (
            id INTEGER PRIMARY KEY,
            domain VARCHAR(255) NOT NULL,
            UNIQUE (domain)
        )"
    );
};

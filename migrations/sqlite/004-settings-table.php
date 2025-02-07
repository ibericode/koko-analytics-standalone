<?php

use App\Database;

return function (Database $db) {
    $db->exec(
        "CREATE TABLE koko_analytics_settings (
            domain_id SMALLINT UNSIGNED NOT NULL,
            name VARCHAR(127) NOT NULL,
            value TEXT NOT NULL,
            PRIMARY KEY (domain_id, name)
        )"
    );
};

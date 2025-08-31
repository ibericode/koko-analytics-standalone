<?php

use App\Database;

return function (Database $db) {
    $db->exec("ALTER TABLE koko_analytics_domains RENAME TO koko_analytics_domains_old");

    $db->exec(
        "CREATE TABLE koko_analytics_domains (
            id INTEGER PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            timezone VARCHAR(255) NOT NULL DEFAULT 'UTC',
            purge_treshold INTEGER NOT NULL DEFAULT 1825,
            excluded_ip_addresses VARCHAR(255) NOT NULL DEFAULT '',
            UNIQUE (name)
        )"
    );

    $db->exec("INSERT INTO koko_analytics_domains(id, name) SELECT id, name FROM koko_analytics_domains_old");
    $db->exec("DROP TABLE koko_analytics_domains_old");
    $db->exec("DROP TABLE koko_analytics_settings");
};

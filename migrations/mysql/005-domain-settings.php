<?php

use App\Database;

return function (Database $db) {
    $db->exec(
        "ALTER TABLE koko_analytics_domains ADD COLUMN timezone VARCHAR(255) NOT NULL DEFAULT 'UTC'"
    );
    $db->exec(
        "ALTER TABLE koko_analytics_domains ADD COLUMN purge_treshold SMALLINT UNSIGNED NOT NULL DEFAULT 1825"
    );
    $db->exec(
        "ALTER TABLE koko_analytics_domains ADD COLUMN excluded_ip_addresses TEXT NOT NULL"
    );
    $db->exec(
        "DROP TABLE koko_analytics_settings"
    );
};

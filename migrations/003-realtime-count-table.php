<?php

use App\Database;

return function(Database $db) {
    $db->exec(
        "CREATE TABLE koko_analytics_realtime_count (
            timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            count SMALLINT UNSIGNED NOT NULL DEFAULT 0
        ) ENGINE=INNODB;"
    );
};

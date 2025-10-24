<?php

use App\Database;

return function (Database $db) {
    $db->exec("ALTER TABLE koko_analytics_domains ADD COLUMN user_id INT UNSIGNED NOT NULL");
    $db->exec("UPDATE koko_analytics_domains SET user_id = (SELECT id FROM koko_analytics_users LIMIT 1)");
};

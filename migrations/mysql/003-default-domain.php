<?php

use App\Database;

return function (Database $db) {
    $db->exec(
        "INSERT INTO koko_analytics_domains (name) VALUES ('website.com');"
    );
    $id = $db->lastInsertId();

    // create dashboard-specific tables for this domain
    $db->exec(
        "CREATE TABLE koko_analytics_site_stats_{$id} (
              date DATE PRIMARY KEY NOT NULL,
              visitors SMALLINT UNSIGNED NOT NULL,
              pageviews SMALLINT UNSIGNED NOT NULL
        ) ENGINE=INNODB CHARACTER SET=ascii"
    );
     $db->exec(
         "CREATE TABLE koko_analytics_page_urls_{$id} (
          id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
          url VARCHAR(255) NOT NULL,
          UNIQUE INDEX (url)
        ) ENGINE=INNODB CHARACTER SET=ascii"
     );
    $db->exec(
        "CREATE TABLE koko_analytics_page_stats_{$id} (
          date DATE NOT NULL,
          id INT UNSIGNED NOT NULL,
          visitors SMALLINT UNSIGNED NOT NULL,
          pageviews SMALLINT UNSIGNED NOT NULL,
          PRIMARY KEY (date, id)
        ) ENGINE=INNODB CHARACTER SET=ascii"
    );
    $db->exec(
        "CREATE TABLE koko_analytics_referrer_urls_{$id} (
          id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
          url VARCHAR(255) NOT NULL,
          UNIQUE INDEX (url)
        ) ENGINE=INNODB CHARACTER SET=ascii"
    );
    $db->exec(
        "CREATE TABLE koko_analytics_referrer_stats_{$id} (
          date DATE NOT NULL,
          id INT UNSIGNED NOT NULL,
          visitors SMALLINT UNSIGNED NOT NULL,
          pageviews SMALLINT UNSIGNED NOT NULL,
          PRIMARY KEY (date, id)
        ) ENGINE=INNODB CHARACTER SET=ascii"
    );
    $db->exec(
        "CREATE TABLE koko_analytics_realtime_count_{$id} (
            timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            count SMALLINT UNSIGNED NOT NULL DEFAULT 0
        ) ENGINE=INNODB;"
    );
};

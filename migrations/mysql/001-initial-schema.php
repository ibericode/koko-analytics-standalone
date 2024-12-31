<?php

use App\Database;

return function(Database $db) {
    $db->exec(
        "CREATE TABLE koko_analytics_site_stats (
              date DATE PRIMARY KEY NOT NULL,
              visitors SMALLINT UNSIGNED NOT NULL,
              pageviews SMALLINT UNSIGNED NOT NULL
        ) ENGINE=INNODB CHARACTER SET=ascii"
    );
     $db->exec(
        "CREATE TABLE koko_analytics_page_urls (
          id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
          url VARCHAR(255) NOT NULL,
          UNIQUE INDEX (url)
        ) ENGINE=INNODB CHARACTER SET=ascii"
    );
    $db->exec(
        "CREATE TABLE koko_analytics_page_stats (
          date DATE NOT NULL,
          id INT UNSIGNED NOT NULL,
          visitors SMALLINT UNSIGNED NOT NULL,
          pageviews SMALLINT UNSIGNED NOT NULL,
          PRIMARY KEY (date, id)
        ) ENGINE=INNODB CHARACTER SET=ascii"
    );
    $db->exec(
        "CREATE TABLE koko_analytics_referrer_urls (
          id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
          url VARCHAR(255) NOT NULL,
          UNIQUE INDEX (url)
        ) ENGINE=INNODB CHARACTER SET=ascii"
    );
    $db->exec(
        "CREATE TABLE koko_analytics_referrer_stats (
          date DATE NOT NULL,
          id INT UNSIGNED NOT NULL,
          visitors SMALLINT UNSIGNED NOT NULL,
          pageviews SMALLINT UNSIGNED NOT NULL,
          PRIMARY KEY (date, id)
        ) ENGINE=INNODB CHARACTER SET=ascii"
    );
};

<?php

use App\Database;

return function(Database $db) {
    $db->exec(
        "INSERT INTO koko_analytics_domains (name) VALUES ('website.com');"
    );
    $id = $db->lastInsertId();

    $db->exec(
        "CREATE TABLE koko_analytics_site_stats_{$id} (
          date DATE PRIMARY KEY NOT NULL,
          visitors SMALLINT UNSIGNED NOT NULL,
          pageviews SMALLINT UNSIGNED NOT NULL
        )"
    );
     $db->exec(
        "CREATE TABLE koko_analytics_page_urls_{$id} (
          id INTEGER PRIMARY KEY,
          url VARCHAR(255) NOT NULL,
          UNIQUE (url)
        )"
    );
    $db->exec(
        "CREATE TABLE koko_analytics_page_stats_{$id} (
          date DATE NOT NULL,
          id INTEGER NOT NULL,
          visitors SMALLINT UNSIGNED NOT NULL,
          pageviews SMALLINT UNSIGNED NOT NULL,
          PRIMARY KEY (date, id)
        )"
    );
    $db->exec(
        "CREATE TABLE koko_analytics_referrer_urls_{$id} (
          id INTEGER PRIMARY KEY,
          url VARCHAR(255) NOT NULL,
          UNIQUE (url)
        )"
    );
    $db->exec(
        "CREATE TABLE koko_analytics_referrer_stats_{$id} (
          date DATE NOT NULL,
          id INTEGER NOT NULL,
          visitors SMALLINT UNSIGNED NOT NULL,
          pageviews SMALLINT UNSIGNED NOT NULL,
          PRIMARY KEY (date, id)
        )"
    );
     $db->exec(
        "CREATE TABLE koko_analytics_realtime_count_{$id} (
            timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            count SMALLINT UNSIGNED NOT NULL DEFAULT 0
        )"
    );
};

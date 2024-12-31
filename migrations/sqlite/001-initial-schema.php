<?php

use App\Database;

return function(Database $db) {
    $db->exec(
        "CREATE TABLE koko_analytics_site_stats (
          date DATE PRIMARY KEY NOT NULL,
          visitors SMALLINT UNSIGNED NOT NULL,
          pageviews SMALLINT UNSIGNED NOT NULL
        )"
    );
     $db->exec(
        "CREATE TABLE koko_analytics_page_urls (
          id INTEGER PRIMARY KEY,
          url VARCHAR(255) NOT NULL,
          UNIQUE (url)
        )"
    );
    $db->exec(
        "CREATE TABLE koko_analytics_page_stats (
          date DATE NOT NULL,
          id INTEGER NOT NULL,
          visitors SMALLINT UNSIGNED NOT NULL,
          pageviews SMALLINT UNSIGNED NOT NULL,
          PRIMARY KEY (date, id)
        )"
    );
    $db->exec(
        "CREATE TABLE koko_analytics_referrer_urls (
          id INTEGER PRIMARY KEY,
          url VARCHAR(255) NOT NULL,
          UNIQUE (url)
        )"
    );
    $db->exec(
        "CREATE TABLE koko_analytics_referrer_stats (
          date DATE NOT NULL,
          id INTEGER NOT NULL,
          visitors SMALLINT UNSIGNED NOT NULL,
          pageviews SMALLINT UNSIGNED NOT NULL,
          PRIMARY KEY (date, id)
        )"
    );
};

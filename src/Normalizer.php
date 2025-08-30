<?php

namespace App;

class Normalizer
{
    public function path($value): string
    {
        if (!$value) {
            return '';
        }

        // lowercase value
        $value = strtolower($value);
        $value = substr($value, 0, 255);

        // remove # from URL
        if (($pos = strpos($value, '#')) !== false) {
            $value = substr($value, 0, $pos);
        }

        // if URL contains query string, parse it and only keep certain parameters
        if (($pos = strpos($value, '?')) !== false) {
            $query_str = substr($value, $pos + 1);
            $value = substr($value, 0, $pos + 1);

            $params = [];
            parse_str($query_str, $params);
            $value .= http_build_query(array_intersect_key($params, ['page_id' => 1, 'p' => 1, 'tag' => 1, 'cat' => 1, 'product' => 1, 'attachment_id' => 1, 's' => 1]));

            // trim trailing question mark & replace url with new sanitized url
            $value = rtrim($value, '?');
        }

        return $value;
    }

    public function referrer($value): string
    {
        if (!$value) {
            return '';
        }

        // lowercase referrer
        $value = strtolower($value);

        // take first 255 chars
        $value = substr($value, 0, 255);

        // aggregate certain sources into a single entry
        static $aggregations = [
            // replace most android apps with their web-equivalent
            '/^android-app:\/\/(\w{2,3})(\.www)?\.(\w+).*/' => 'https://$3.$1',
            '/^android-app:\/\/m\.facebook\.com/' => 'https://facebook.com',

            // popular iOS apps
            '/^ios-app:\/\/429047995.*/' => 'https://pinterest.com',
            '/^ios-app:\/\/1064216828.*/' => 'https://reddit.com',
            '/^ios-app:\/\/284882215.*/' => 'https://facebook.com',
            '/^ios-app:\/\/389801252.*/' => 'https://instagram.com',

            // popular websites
            '/^https?:\/\/(?:www\.)?(google|bing|ecosia)\.([a-z]{2,4}(?:\.[a-z]{2,4})?)(?:\/search|\/url)?/' => 'https://$1.$2',
            '/^https?:\/\/(?:[a-z-]+\.)?l?facebook\.com(?:\/l\.php)?/' => 'https://facebook.com',
            '/^https?:\/\/(?:[a-z-]+\.)?l?instagram\.com(?:\/l\.php)?/' => 'https://instagram.com',
            '/^https?:\/\/(?:[a-z-]+\.)?linkedin\.com\/feed.*/' => 'https://linkedin.com',
            '/^https?:\/\/(?:[a-z-]+\.)?pinterest\.com/' => 'https://pinterest.com',
            '/^https?:\/\/(?:[a-z-]+\.)?baidu\.com.*/' => 'https://baidu.com',
            '/^https?:\/\/(?:[a-z-]+\.)?yandex\.ru\/.*/' => 'https://yandex.ru',
            '/^https?:\/\/(?:[a-z-]+\.)?search\.yahoo\.com\/.*/' => 'https://search.yahoo.com',
            '/^https?:\/\/(?:[a-z-]+\.)?reddit\.com.*/' => 'https://reddit.com',
            '/^https?:\/\/(?:[a-z0-9]{1,8}\.)+sendib(?:m|t)[0-9]\.com.*/' => 'https://brevo.com',
        ];
        $normalized_value = (string) preg_replace(array_keys($aggregations), array_values($aggregations), $value, 1);
        if (preg_last_error() === PREG_NO_ERROR) {
            $value = $normalized_value;
        }

        // limit resulting value to just host
        $url_parts = parse_url($value);
        if ($url_parts === false || empty($url_parts['host'])) {
            return '';
        }
        $value = $url_parts['host'];

        // strip www. prefix
        if (str_starts_with($value, 'www.')) {
            $value = substr($value, 4);
        }

        // add path if domain is whitelisted
        $whitelisted_domains = ['kokoanalytics.com', 'github.com'];
        if (in_array($value, $whitelisted_domains) && !empty($url_parts['path']) && $url_parts['path'] !== '/') {
            $value .= $url_parts['path'];
        }

        return $value;
    }
}

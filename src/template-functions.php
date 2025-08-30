<?php

function esc(string $value): string
{
    if (str_starts_with($value, 'javascript:')) {
        $value = substr($value, strlen('javascript:'));
    }

    return \htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function percent_format($pct): string
{
    if ($pct == 0) {
        return '';
    }

    $prefix = $pct > 0 ? '+' : '';
    $formatted = \number_format($pct * 100, 0);
    return $prefix . $formatted . '%';
}


function get_referrer_url_label(string $url): string
{
    // if link starts with android-app://, turn that prefix into something more human readable
    if (\strpos($url, 'android-app://') === 0) {
        return \str_replace('android-app://', 'Android app: ', $url);
    }

    // strip protocol and www. prefix
    $url = (string) \preg_replace('/^https?:\/\/(?:www\.)?/', '', $url);

    // trim trailing slash
    return \rtrim($url, '/');
}

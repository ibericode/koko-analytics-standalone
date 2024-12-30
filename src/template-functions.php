<?php

function esc(string $value) : string 
{ 
    return \htmlspecialchars($value, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8');
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

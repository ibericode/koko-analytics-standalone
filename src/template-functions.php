<?php

function esc(string $value) : string 
{ 
    return htmlspecialchars($value, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8');
}

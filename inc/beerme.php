<?php
function address($a, $c, $s, $l)
{
    $r = "";

    if ($a) $r .= "{$a}, ";

    $r .= "{$c}, ";

    if ($l == "United States" || $l == "Canada")
        $r .= $s;
    else
        $r .= $l;

    return $r;
}

function boolToText($b)
{
    if ($b) return "true";
    else return "false";
}

// Services
define("OPEN", 0x0001);
define("BAR", 0x0002);
define("BEERGARDEN", 0x0004);
define("FOOD", 0x0008);
define("GIFTSHOP", 0x0010);
define("HOTEL", 0x0020);
define("INTERNET", 0x0040);
define("RETAIL", 0x0080);
define("TOURS", 0x0100);

<?php

function get_sequential_id($sequential_pad = 0)
{
    $tm = microtime(true) * 10000;
    $rd = rand(100000, 999999);
    $hx = $tm . str_pad($sequential_pad, 6, "0", STR_PAD_LEFT) . $rd;

    return $hx;
}

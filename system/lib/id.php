<?php

function get_sequential_id()
{
    $tm = microtime(true) * 10000;
    $rd = rand(100000, 999999);
    $hx = $tm . $rd;

    return $hx;
}

<?php

function is_valid_name($name) {
    preg_match_all("/[a-zA-Z0-9-.]+/", $name, $matches);
    return count($matches) > 0;
}
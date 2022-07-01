<?php

function env($key, $default_value = null) {
    $env_text = file_get_contents(INPHINIT_ROOT . "environment.json");
    return json_decode($env_text, true)[$key] ?? $default_value;
}
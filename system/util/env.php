<?php

function env()
{
    $env_text = file_get_contents(INPHINIT_ROOT . "environment.json");
    return json_decode($env_text, true);
}

$GLOBALS["env"] = env();

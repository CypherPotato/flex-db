<?php

function perm($permission): int
{
    $token = $_SERVER["HTTP_TOKEN"] ?? "";
    $tokens = env("tokens", []);
    $ftoken = [];
    foreach ($tokens as $xtoken) {
        if ($token == $xtoken["token"]) {
            $ftoken = $xtoken;
        }
    }

    if ($ftoken == []) {
        return -1;
    }
    $c = 0;
    foreach ($ftoken["permissions"] as $perm) {
        if ($perm == "*") {
            return 1;
        }

        $pattern = $perm;
        $pattern = str_replace(".", "\.", $pattern);
        $pattern = str_replace("*", ".*", $pattern);

        preg_match("/$pattern/", $permission, $match);
        if (count($match) >= 1) {
            $c = 1;
            break;
        }
    }

    return $c;
}

function validate_permission($permission, $close = true)
{
    if (perm($permission) != 1) {
        add_message("error", "Not authorized to perform this operation.");
        if ($close) {
            json_response(null, true);
            die();
        }
        return false;
    }
    return true;
}

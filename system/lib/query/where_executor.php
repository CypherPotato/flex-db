<?php

function execute_where($field, $pattern, $object): bool
{
    if ($pattern == "not-null" && isset($object[$field]) && $object[$field] != null) {
        return true;
    }
    if (!isset($object[$field])) {
        if ($pattern == "null") {
            return true;
        } else {
            return false;
        }
    }
    $value = $object[$field];
    $pattern_clean = substr($pattern, 1);
    if (str_starts_with($pattern, "^")) { // starts_with
        return str_starts_with($value, $pattern_clean);
    } else if (str_starts_with($pattern, "$")) { // ends_with
        return str_ends_with($value, $pattern_clean);
    } else if (str_starts_with($pattern, "~")) { // contains
        return str_contains($value, $pattern_clean);
    } else if (str_starts_with($pattern, ">=")) { // bigger than
        $pattern_clean = substr($pattern, 2);
        return $value >= $pattern_clean;
    } else if (str_starts_with($pattern, "<=")) { // bigger or equals than
        $pattern_clean = substr($pattern, 2);
        return $value <= $pattern_clean;
    } else if (str_starts_with($pattern, "<")) { // greater than
        return $value < $pattern_clean;
    } else if (str_starts_with($pattern, ">")) { // greater or equals than
        return $value > $pattern_clean;
    } else if (str_starts_with($pattern, "/")) { // regex
        preg_match($pattern, $value, $match);
        return $match > 0;
    } else {
        return $pattern == $value;
    }
}

<?php

/**
 * Matching symbols meanings
 * ^    STARTS-WITH
 * $    ENDS-WITH
 * ~    CONTAINS
 * >=   BIGGER-OR-EQUALS-THAN
 * >    BIGGER-THAN
 * <=   GREATER-OR-EQUALS-THAN
 * <    GREATER-THAN
 * /    REGEX
 */

function execute_where($field, $pattern, $object, $filters): bool
{
    if ($pattern == ":not-null" && isset($object[$field]) && $object[$field] != null) {
        return true;
    }
    if (!isset($object[$field])) {
        if ($pattern == ":null") {
            return true;
        } else {
            return false;
        }
    }

    $value = $object[$field];
    $pattern_clean = substr($pattern, 1);

    if (in_array("ignore-case", $filters)) {
        $pattern_clean = strtolower($pattern_clean);
        $value = strtolower($value);
    }
    if (in_array("trim", $filters)) {
        $pattern_clean = trim($pattern_clean);
        $value = trim($value);
    }
    if (in_array("sanitize-numbers", $filters)) {
        $pattern_clean = preg_replace("[^0-9]", "", $pattern_clean);
        $value = preg_replace("[^0-9]", "", $value);
    }

    if (str_starts_with($pattern, "^")) {
        return str_starts_with($value, $pattern_clean);
    } else if (str_starts_with($pattern, "$")) {
        return str_ends_with($value, $pattern_clean);
    } else if (str_starts_with($pattern, "~")) {
        return str_contains($value, $pattern_clean);
    } else if (str_starts_with($pattern, ">=")) {
        $pattern_clean = substr($pattern, 2);
        return $value >= $pattern_clean;
    } else if (str_starts_with($pattern, "<=")) {
        $pattern_clean = substr($pattern, 2);
        return $value <= $pattern_clean;
    } else if (str_starts_with($pattern, "<")) {
        return $value < $pattern_clean;
    } else if (str_starts_with($pattern, ">")) {
        return $value > $pattern_clean;
    } else if (str_starts_with($pattern, "/")) {
        preg_match($pattern, $value, $match);
        return $match > 0;
    } else {
        return $pattern == $value;
    }
}

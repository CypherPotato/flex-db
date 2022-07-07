<?php

function flex_query_decode($string)
{
    $root_document = substr($string, 1, strlen($string) - 2);
    $has_arguments = str_contains($root_document, "(");

    if ($has_arguments) {
        $collection = trim(explode("(", $root_document)[0]);
        $arguments = "{" . substr($root_document, $arg_str_b = strpos($root_document, "(") + 1, strrpos($root_document, ")") - $arg_str_b) . "}";
        $select = trim(substr($t = trim(explode(")", $root_document)[1]), 1, strlen($t) - 2));
    } else {
        $arguments = "{}";
        $collection = trim(explode("{", $root_document)[0]);
        $select = trim(substr($t = trim(explode("{", $root_document)[1]), 0, strlen($t) - 1));
    }

    $select = array_map('trim', explode("\n", $select));
    $args = json5_decode($arguments, true);
    
    $obj_return = new stdClass;
    $obj_return->select = $select;
    $obj_return->collection = $collection;
    $obj_return->pagination = new stdClass;
    if (isset($args["where"] )) $obj_return->where = $args["where"];
    if (isset($args["skip"] )) $obj_return->pagination->skip = $args["skip"];
    if (isset($args["take"] )) $obj_return->pagination->take = $args["take"];
    if (isset($args["normalize"] )) $obj_return->normalize = $args["normalize"];
    if (isset($args["order_by"] )) $obj_return->order_by = $args["order_by"];
    if (isset($args["order_term"] )) $obj_return->order_term = $args["order_term"];

    return $obj_return;
}

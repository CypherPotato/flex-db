<?php

function build_storage_object($id, $content)
{
    if (is_array($content)) {
        return json_encode(["id" => $id] + $content);
    } else {
        return json_encode(["id" => $id] + get_object_vars($content));
    }
}

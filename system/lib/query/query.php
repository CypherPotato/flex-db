<?php

function run_query($query): array
{
    require_param($query->collection, "collection");
    validate_permission("collection." . $query->collection . ".read");
    
    $output = [];
    $collection_root = STORAGE_PATH . $query->collection;
    if (!is_dir($collection_root)) {
        add_message("error", "Target collection does not exists.");
        return json_response();
    }

    $schema = json_decode(file_get_contents($collection_root . "/collection.json"), true)["schema"];

    if (isset($query->pagination)) {
        $skip = $query->pagination->skip ?? 0;
        $take = $query->pagination->take ?? 2147483647;
    }

    $select_all = in_array("*", $query->select ?? []);
    $index = -$skip;
    $normalize = $query->normalize ?? false;

    if ($handle = opendir($collection_root . "/data/")) {
        while (false !== ($file = readdir($handle))) {
            if ($file == "." || $file == "..") continue;

            $file_contents = file_get_contents($collection_root . "/data/" . $file);
            $object = json_decode($file_contents, true);

            if (isset($query->where)) {
                foreach ($query->where as $field => $pattern) {
                    if (!execute_where($field, $pattern, $object)) {
                        continue 2;
                    }
                }
            }

            if (!$select_all && isset($query->select)) {
                $object = array_intersect_key($object, array_flip($query->select));
            }

            $index++;
            if ($index <= 0) {
                continue;
            }
            if ($index > $take) {
                break;
            }

            if ($normalize) {
                $output[] = array_intersect_key($object, $schema + ["id" => ""]);
            } else {
                $output[] = $object;
            }
        }

        closedir($handle);
    }

    return $output;
}

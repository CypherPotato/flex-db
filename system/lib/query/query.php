<?php

function normalize_by_schema(array $object, array $schema)
{
    $OBJECT = [];
    foreach ($schema as $schema_key => $schema_value) {
        if (!key_exists($schema_key, $object)) {
            $OBJECT[$schema_key] = null;
        }
    }
    return $object + $OBJECT;
}

function run_query($query): array
{
    require_param($query->collection, "collection");
    validate_permission("collection." . $query->collection . ".read");

    $output = [];
    $collection_root = STORAGE_PATH . $query->collection;
    if (!is_dir($collection_root)) {
        add_message("error", "Target collection does not exists.");
        return [];
    }

    $collection = json_decode(file_get_contents($collection_root . "/collection.json"), true);
    $schema = $collection["schema"];

    if (isset($query->pagination->skip) || isset($query->pagination->take)) {
        $skip = $query->pagination->skip ?? 0;
        $take = $query->pagination->take ?? 2147483647;
    } else {
        $skip = 0;
        $take = 2147483647;
    }

    $select_all = in_array("*", $query->select ?? []);
    $index = -$skip;
    $normalize = $query->normalize ?? false;
    $filter = $query->filters ?? [];

    if ($normalize && ($collection["dynamic"] ?? false)) {
        add_message("error", "Cannot normalize an dynamic collection.");
        return [];
    }

    if ($handle = opendir($collection_root . "/data/")) {
        while (false !== ($file = readdir($handle))) {
            if ($file == "." || $file == "..") continue;

            $file_path = $collection_root . "/data/" . $file;
            $file_contents = file_get_contents($file_path);
            $object = [
                "created_at" => filectime($file_path),
                "updated_at" => filemtime($file_path)
            ] + json_decode($file_contents, true);

            if (isset($query->where)) {
                foreach ($query->where as $field => $pattern) {
                    if (!execute_where($field, $pattern, $object, $filter)) {
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
                if ($select_all) {
                    $object = normalize_by_schema($object, $schema);
                } else {
                    $intersection = $schema + ["id" => "", "created_at" => "", "updated_at" => ""];
                    array_intersect_key($object, $intersection);
                }
            }
            $output[] = $object;
        }

        closedir($handle);

        //order results
        if (isset($query->order_by)) {
            $order_term = $query->order_term ?? "asc";
            $order_field = $query->order_by;
            usort($output, function ($a, $b) use ($order_field, $order_term) {
                $a[$order_field] ??= "";
                $b[$order_field] ??= "";
                if (strtolower($order_term) == "desc") {
                    return strcmp($b[$order_field], $a[$order_field]);
                } else {
                    return strcmp($a[$order_field], $b[$order_field]);
                }
            });
        }
    }

    return $output;
}

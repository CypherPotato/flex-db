<?php

namespace Controller;

class Collections
{
    public function browse()
    {
        $collections = array_values(array_diff(scandir(STORAGE_PATH), [".", ".."]));
        $matched_with_permission = [];
        for ($i = 0; $i < count($collections); $i++) {
            $collection = $collections[$i];
            if (validate_permission("collection." . $collection . ".read", false)) {
                $matched_with_permission [] = ($collection);
            }
        }
        return json_response(["collections" => $matched_with_permission]);
    }

    public function read($collection)
    {
        validate_permission("collection.$collection.read");

        $collection_root = STORAGE_PATH . $collection;
        if (!is_dir($collection_root)) {
            add_message("error", "Collection not found.");
            return json_response();
        }

        $mcollection_data = json_decode(file_get_contents($collection_root . "/collection.json"), true);

        return json_response(["collection" => $mcollection_data]);
    }

    public function add()
    {
        require_param($GLOBALS["request"]->collection, "collection");
        require_param($GLOBALS["request"]->collection->name, "collection.name");
        require_param($GLOBALS["request"]->schema, "schema");

        $mcollection_data = [];
        $mcollection_data["name"] = $GLOBALS["request"]->collection->name;
        $mcollection_data["dynamic"] = $GLOBALS["request"]->collection->dynamic ?? false;

        validate_permission("collection." . $mcollection_data["name"]  . ".write");

        if ($mcollection_data["dynamic"]) {
            $mcollection_data["schema"] = new \stdClass;
        } else {
            $mcollection_data["schema"] = $GLOBALS["request"]->schema;
            $schema_built = \Schema::build($GLOBALS["request"]->schema);
            if ($schema_built == false) {
                add_message("error", "Invalid collection schema syntax.");
                return json_response();
            }
        }

        if (!is_valid_name($mcollection_data["name"])) {
            add_message("error", "Invalid collection name: " . $GLOBALS["request"]->collection->name);
            return json_response();
        }

        $collection_root = STORAGE_PATH . $mcollection_data["name"];
        if (is_dir($collection_root)) {
            add_message("error", "Collection already exists.");
            return json_response();
        }

        umkdir($collection_root, STORAGE_PERMISSION_LEVEL);
        umkdir($collection_root . "/data", STORAGE_PERMISSION_LEVEL);
        file_put_contents($collection_root . "/collection.json", json_encode($mcollection_data));

        add_message("info", "Collection created succesfully.");
        return json_response();
    }

    public function edit()
    {
        require_param($GLOBALS["request"]->collection, "collection");
        require_param($GLOBALS["request"]->collection->name, "collection.name");
        require_param($GLOBALS["request"]->schema, "schema");

        $collection_name = $GLOBALS["request"]->collection->name;

        validate_permission("collection.$collection_name.write");

        if (!is_valid_name($collection_name)) {
            add_message("error", "Invalid collection name: " . $collection_name);
            return json_response();
        }

        $collection_root = STORAGE_PATH . $collection_name;

        if (!is_dir($collection_root)) {
            add_message("error", "Collection not found.");
            return json_response();
        }

        $mcollection_data = json_decode(file_get_contents($collection_root . "/collection.json"), true);

        $schema_built = \Schema::build($GLOBALS["request"]->schema);
        if ($schema_built == false) {
            add_message("error", "Invalid collection schema syntax.");
            return json_response();
        }

        $mcollection_data["schema"] = $GLOBALS["request"]->schema;

        file_put_contents($collection_root . "/collection.json", json_encode($mcollection_data));

        add_message("info", "Collection edited succesfully.");
        return json_response();
    }

    public function delete()
    {
        require_param($GLOBALS["request"]->collection->name, "collection.name");
        $collection_name = $GLOBALS["request"]->collection->name;
        $collection_root = STORAGE_PATH . $collection_name;

        validate_permission("collection.$collection_name.write");

        if (!is_dir($collection_root)) {
            add_message("error", "Collection not found.");
            return json_response();
        }

        recurse_rm_dir($collection_root);

        if (is_dir($collection_root)) {
            add_message("error", "Failed to remove collection.");
        } else {
            add_message("info", "Collection removed succesfully.");
        }

        return json_response();
    }
}

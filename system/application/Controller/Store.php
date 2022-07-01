<?php

namespace Controller;

class Store
{
    public function add()
    {
        require_param($GLOBALS["request"]->collection, "collection");
        require_param($GLOBALS["request"]->collection->name, "collection.name");
        require_param($GLOBALS["request"]->object, "object");
        require_param($GLOBALS["request"]->object->contents, "object.contents");

        $collection_name = $GLOBALS["request"]->collection->name;
        $data_contents = $GLOBALS["request"]->object->contents;

        validate_permission("collection." . $collection_name  . ".write");

        $collection_root = STORAGE_PATH . $collection_name;
        if (!is_dir($collection_root)) {
            add_message("error", "Target collection does not exists.");
            return json_response();
        }
        $collection_schema = json_decode(file_get_contents($collection_root . "/collection.json"), true)["schema"];

        $validationResult = \Schema::validate($data_contents, $collection_schema);
        if ($validationResult == false) {
            return json_response();
        }

        $id = get_sequential_id();
        file_put_contents($collection_root . "/data/$id.json", build_storage_object($id, $data_contents));

        add_message("info", "Object successfully stored.");
        return json_response(["id" => $id]);
    }

    public function edit()
    {
        require_param($GLOBALS["request"]->collection, "collection");
        require_param($GLOBALS["request"]->collection->name, "collection.name");
        require_param($GLOBALS["request"]->object, "object");
        require_param($GLOBALS["request"]->object->id, "object.id");
        require_param($GLOBALS["request"]->object->contents, "object.contents");

        $collection_name = $GLOBALS["request"]->collection->name;
        $id = $GLOBALS["request"]->object->id;
        $data_contents = $GLOBALS["request"]->object->contents;

        validate_permission("collection." . $collection_name  . ".write");

        $collection_root = STORAGE_PATH . $collection_name;
        if (!is_dir($collection_root)) {
            add_message("error", "Target collection does not exists.");
            return json_response();
        }
        $collection_schema = json_decode(file_get_contents($collection_root . "/collection.json"), true)["schema"];

        $file_path = $collection_root . "/data/$id.json";
        if (!is_file($file_path)) {
            add_message("error", "Specified id was not found in this collection.");
            return json_response();
        }

        $original_contents = json_decode(file_get_contents($file_path), true);
        $data_contents = array_diff_key(array_merge($original_contents, (array)$data_contents), ["id" => ""]);

        $validationResult = \Schema::validate($data_contents, $collection_schema);
        if ($validationResult == false) {
            return json_response();
        }

        $file = $collection_root . "/data/$id.json";
        if (!is_file($file)) {
            add_message("error", "No object with the specified id was found.");
            return json_response();
        }
        file_put_contents($file, build_storage_object($id, $data_contents));

        add_message("info", "Object successfully updated.");
        return json_response(["id" => $id]);
    }

    public function patch()
    {
        require_param($GLOBALS["request"]->collection, "collection");
        require_param($GLOBALS["request"]->collection->name, "collection.name");
        require_param($GLOBALS["request"]->objects, "object");

        $collection_name = $GLOBALS["request"]->collection->name;
        $collection_root = STORAGE_PATH . $collection_name;

        validate_permission("collection." . $collection_name  . ".write");

        if (!is_dir($collection_root)) {
            add_message("error", "Target collection does not exists.");
            return json_response();
        }
        $collection_schema = json_decode(file_get_contents($collection_root . "/collection.json"), true)["schema"];

        $objects = $GLOBALS["request"]->objects;
        $inserted = [];

        $skip_not_expected_fields = $GLOBALS["request"]->options->skip_not_expected_fields ?? false;
        if ($GLOBALS["request"]->options->supress_warnings ?? false) {
            $GLOBALS["supress-messaging"] = true;
        } else {
            $GLOBALS["supress-messaging"] = false;
        }

        foreach ($objects as $object) {
            $validationResult = \Schema::validate($object, $collection_schema, $skip_not_expected_fields, true);
            if ($validationResult == false) {
                continue;
            }

            $id = get_sequential_id();
            file_put_contents($collection_root . "/data/$id.json", build_storage_object($id, $object));
            $inserted[] = $id;
        }
        $GLOBALS["supress-messaging"] = false;

        add_message("info", count($inserted) . " of " . count($objects) . " was inserted in the collection.");
        return json_response(["inserted_ids" => $inserted]);
    }

    public function delete()
    {
        require_param($GLOBALS["request"]->collection, "collection");
        require_param($GLOBALS["request"]->collection->name, "collection.name");
        require_param($GLOBALS["request"]->object, "object");
        require_param($GLOBALS["request"]->object->id, "object.id");

        $collection_name = $GLOBALS["request"]->collection->name;
        $id = $GLOBALS["request"]->object->id;

        validate_permission("collection." . $collection_name  . ".write");

        $collection_root = STORAGE_PATH . $collection_name;
        if (!is_dir($collection_root)) {
            add_message("error", "Target collection does not exists.");
            return json_response();
        }
        $object_path = $collection_root . "/data/$id.json";

        if (is_file($object_path)) {
            unlink($object_path);
            add_message("info", "Object successfully deleted.");
        } else {
            add_message("error", "Cannot find specified object.");
        }

        return json_response();
    }
}

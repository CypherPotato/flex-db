<?php

namespace schema;

trait validate
{
    public static function validate($dataContents, $schema, $skip_not_expected_fields = false, $messages_as_warnings = false): bool
    {
        $tokens = \Schema::build($schema);
        if ($tokens == false) return false;
        $message_level = $messages_as_warnings ? "warn" : "error";

        $tokens_keys = array_fill_keys(array_keys($tokens), false);
        foreach ($dataContents as $key => $value) {
            if (!in_array($key, array_keys($tokens_keys))) {
                if ($skip_not_expected_fields) {
                    unset($dataContents->$key);
                    continue;
                } else {
                    add_message($message_level, "Field '$key' wasn't expected in the schema.");
                    return false;
                }
            } else if ($tokens_keys[$key] == true) {
                add_message($message_level, "Field '$key' has already been sent in this request.");
                return false;
            } else {
                $tokens_keys[$key] = true;
            }

            if (!$tokens[$key]["nullable"] && is_null($value)) {
                add_message($message_level, "Field '$key' cannot be null.");
                return false;
            }

            if ($tokens[$key]["pattern"] == OBJECT_PATTERN && !(is_object($value) ^ is_array($value))) {
                add_message($message_level, "Field '$key' expected an object.");
                return false;
            }

            if ($tokens[$key]["pattern"] == BOOL_PATTERN) {
                if (!is_bool($value)) {
                    add_message($message_level, "Field '$key' does't match the expected field pattern.");
                    return false;
                }
            } else if ($tokens[$key]["pattern"] != OBJECT_PATTERN) {
                preg_match($tokens[$key]["pattern"], $value ?? "", $match);
                if (count($match) == 0 && !$tokens[$key]["nullable"]) {
                    add_message($message_level, "Field '$key' does't match the expected field pattern.");
                    return false;
                }
            }
        }

        $missing = array_filter($tokens_keys, fn ($v, $k) => $v == false, ARRAY_FILTER_USE_BOTH);
        // remove non required
        foreach ($tokens as $token_name => $token_data) {
            if ($token_data["nullable"] == true) {
                unset($missing[$token_name]);
            }
        }
        if (count($missing) > 0) {
            add_message($message_level, "Required fields are missing from collection schema: " . join(", ", array_keys($missing)));
            return false;
        }

        return true;
    }
}

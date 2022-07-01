<?php

namespace schema;

trait build
{
    public static function build(object|array $schema): array|bool
    {
        $output = [];
        foreach ($schema as $name => $accept) {
            preg_match("/[^a-zA-Z0-9_\-.]+/", $name, $name_validation_result);

            if (count($name_validation_result) > 0) {
                add_message("error", "Invalid schema field name: \"" . $name . "\".");
                return false;
            }

            if ($name == "id") {
                add_message("error", "Illegal schema field name: \"" . $name . "\".");
                return false;
            }

            $accept_clean = str_replace("?", "", $accept);
            if (str_ends_with($accept, "?")) {
                $output[$name]["nullable"] = true;
            } else {
                $output[$name]["nullable"] = false;
            }

            if (empty($accept)) {
                add_message("error", "\"Accept\" property cannot be null or empty at field \"" . $name . "\".");
                return false;
            }

            switch ($accept_clean) {
                case "object":
                    $output[$name]["pattern"] = OBJECT_PATTERN;
                    break;
                case "string":
                    $output[$name]["pattern"] = STRING_PATTERN;
                    break;
                case "int":
                    $output[$name]["pattern"] = INT_PATTERN;
                    break;
                case "uint":
                    $output[$name]["pattern"] = UINT_PATTERN;
                    break;
                case "number":
                    $output[$name]["pattern"] = NUMBER_PATTERN;
                    break;
                case "bool":
                    $output[$name]["pattern"] = BOOL_PATTERN;
                    break;
                default:
                    if (!str_starts_with($accept, "/") || !str_ends_with($accept, "/")) {
                        add_message("error", "Invalid accept property at field \"" . $name . "\".");
                        return false;
                    } else {
                        $output[$name] = $accept; // parse as regex
                    }
                    break;
            }
        }

        return $output;
    }
}

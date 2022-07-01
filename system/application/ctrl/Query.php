<?php

namespace Controller;

class Query
{
    public function run()
    {
        $permission = validate_permission("query.run", false);
        if (!$permission) {
            return error_response();
        }

        $parsed_query = run_query($GLOBALS["request"]);
        if ($GLOBALS["success"] == false) {
            return error_response();
        }
        return json_response($parsed_query, false, true);
    }
}

<?php

use Inphinit\Http\Response;

$GLOBALS["success"] = true;
$GLOBALS["messages"] = [];
$GLOBALS["supress-messaging"] = false;

$__input = file_get_contents('php://input');
$GLOBALS["raw_request"] = $__input;
if (!empty($__input) && $_SERVER['REQUEST_METHOD'] != "GET") {
    switch ($_SERVER["CONTENT_TYPE"]) {
        case "application/json":
            $GLOBALS["request"] = json_decode($__input, false);
            break;
        case "application/flex-query":
            $GLOBALS["request"] = flex_query_decode($__input);
            break;
        default:
            add_message("error", "Invalid Content-Type received.");
            json_response(null, true);
            break;
    }
}

function require_param(&$expression, $name)
{
    if (!isset($expression)) {
        add_message("error", "Missing parameter: " . $name);
        json_response(null, true);
        die();
    }
}

function safe_get_useragent()
{
    if (!isset($_SERVER['HTTP_USER_AGENT'])) {
        return "unknown";
    } else {
        return $_SERVER['HTTP_USER_AGENT'];
    }
}

function add_message(string $type, string $message)
{
    if ($GLOBALS["supress-messaging"]) return;
    $GLOBALS["messages"][] = [
        "level" => $type,
        "message" => $message
    ];
    if ($type == "error" || $type == "fatal") {
        $GLOBALS["success"] = false;
    }
}

function json_response($content = null, bool $close_connection = false, $raw = false)
{
    header("Content-Type: application/json");

    if ($GLOBALS["success"] == false) {
        http_response_code(400);
    } else {
        http_response_code(200);
    }

    if ($raw) {
        $json_response = json_encode($content);
    } else {
        $json_response = json_encode([
            'success' => $GLOBALS["success"],
            'messages' => array_reverse($GLOBALS["messages"]),
            'response' => $content
        ]);
    }

    if (!$close_connection) {
        return $json_response;
    } else {
        ob_end_clean();
        header("Connection: close");
        ignore_user_abort(true);
        ob_start();
        echo $json_response;
        $size = ob_get_length();
        header("Content-Length: $size");
        ob_end_flush();
        flush();
    }
}

function error_response()
{
    return json_response([
        "error" => $GLOBALS["messages"][0]["message"]
    ], false, true);
}

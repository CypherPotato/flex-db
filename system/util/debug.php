<?php

function dd(...$expression)
{
    echo "<style>pre {white-space: pre-wrap; background-color: whitesmoke; border: 1px solid gainsboro; padding: 5px; word-wrap: break-all; font-family: Cascadia Code, Consolas, monospace; font-size: 14px;}</style>";

    foreach ($expression as $exp) {
        echo "<pre>";
        var_dump($exp);
        echo "</pre>";
    }
    die();
}

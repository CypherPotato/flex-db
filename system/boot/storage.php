<?php

define('STORAGE_PERMISSION_LEVEL', 0777);
define('STORAGE_PATH', INPHINIT_ROOT . 'storage/');

function umkdir(string $path, int $perm): bool
{
    $prev = umask(0);
    $res = \mkdir($path, $perm, true);
    umask($prev);
    return $res;
}

if (!is_dir(STORAGE_PATH)) {
    umkdir(STORAGE_PATH, STORAGE_PERMISSION_LEVEL) or die("Cannot create storage directory");
}
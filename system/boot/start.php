<?php

use Inphinit\App;

require_once INPHINIT_PATH . 'vendor/inphinit/framework/src/Utils.php';

//require_once INPHINIT_PATH . 'vendor/autoload.php';
UtilsAutoload();

require_once INPHINIT_PATH . 'boot/storage.php';

// Require util classes
foreach (glob(INPHINIT_PATH . "util/*.php") as $file) {
    require_once $file;
}

require_once INPHINIT_PATH . 'lib/__driver.php';
require_once INPHINIT_PATH . 'routing.php';

App::exec();
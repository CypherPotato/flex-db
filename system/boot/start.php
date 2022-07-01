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

require_once INPHINIT_PATH . 'lib/constant/datatypes.php';
require_once INPHINIT_PATH . 'lib/query/where_executor.php';
require_once INPHINIT_PATH . 'lib/query/query.php';
require_once INPHINIT_PATH . 'lib/schema/builder.php';
require_once INPHINIT_PATH . 'lib/schema/validator.php';
require_once INPHINIT_PATH . 'lib/schema/schema.php';
require_once INPHINIT_PATH . 'lib/id.php';
require_once INPHINIT_PATH . 'lib/io.php';
require_once INPHINIT_PATH . 'lib/name.php';
require_once INPHINIT_PATH . 'lib/permission.php';
require_once INPHINIT_PATH . 'lib/rm.php';

require_once INPHINIT_PATH . 'routing.php';

App::exec();
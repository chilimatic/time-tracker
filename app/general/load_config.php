<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 6/14/14
 * Time: 3:14 PM
 *
 * this file is only so the
 * init.php doesn't get bloated by
 * the config loading which is sadly to be done manual atm
 */

require_once ( (string) APPLICATION_PATH. '/lib/interfaces/singelton.class.php' );
// default config class && config exception
require_once ( (string) APPLICATION_PATH. '/lib/exception/config.class.php' );
// parent class and interface is needed first (interpreter issue)
include_once ( (string) APPLICATION_PATH. '/lib/config/configinterface.class.php' );
include_once ( (string) APPLICATION_PATH. '/lib/config/configfile/parser.class.php' );
include_once ( (string) APPLICATION_PATH. '/lib/config/abstractconfig.class.php' );
// get all node files
foreach ( glob(APPLICATION_PATH. "/lib/node/*.php") as $filename) {
    if (file_exists($filename) && is_readable($filename)) {
        require_once $filename ;
    }
}
// get all config files
foreach ( glob(APPLICATION_PATH. "/lib/config/*.php") as $filename)
{
    if (file_exists($filename) && is_readable($filename)) {
        require_once $filename ;
    }
}

unset($filename);
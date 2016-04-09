<?php
/**
 * Created by PhpStorm.
 * User: shadowdroid
 * Date: 23/09/2014
 * Time: 18:23
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set("display_errors", 1);

require_once __DIR__ . '/../vendor/autoload.php';

define('APPLICATION_PATH', realpath(__DIR__ . '/../'));
define('INCLUDE_ROOT', APPLICATION_PATH);

try {
    /**
     * the $dispatcher is created in the following script below
     */
    require_once __DIR__ . '/../app/general/init.php';


    $application = new \chilimatic\lib\application\HTTPMVC($dispatcher, $dispatcher->get('config', [], true));
    // this is step so people can inject
    $application->init();
    echo $application->getHandler()->getContent();
}
catch (Exception $e)
{

    if (isset($dispatcher)) {
        /**
         * @var $eh \chilimatic\lib\error\Handler
         */
        $eh = $dispatcher->get('error-handler', ['debug' => $config->get('debug')], true)->getClient();
        if ($eh->showError()) {
            $eh->log(
                $e->getMessage(),
                $e->getTraceAsString()
            )->send();
        }
    } else {
        echo 'nothing to concern you with :)';
    }
}

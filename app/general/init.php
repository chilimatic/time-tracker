<?php

use chilimatic\lib\Database\Sql\Mysql\Connection\MySQLConnection;
use chilimatic\lib\Database\Sql\Mysql\Connection\MySQLConnectionSettings;
use chilimatic\lib\Database\Sql\Mysql\Connection\MySQLConnectionStorage;
use chilimatic\lib\Database\Sql\Mysql\MySQL;
use chilimatic\lib\Database\Sql\Orm\EntityManager;

function myErrorHandler($errno, $errstr, $errfile, $errline)
{

    static $eh;
    if (!$eh) {
        $di = \chilimatic\lib\Di\ClosureFactory::getInstance();
        /**
         * @var $eh \chilimatic\lib\error\Handler
         */
        $eh = $di->get(
            'error-handler',
            [
                'debug' => $di->get(
                    'active-config'
                )->get('debug')
            ],
            true
        )->getClient();
    }

    $str = '';

    switch ($errno) {
        case E_USER_ERROR:
            $str .= "<b>My ERROR</b> [$errno] $errstr<br />\n";
            $str .= "  Fatal error on line $errline in file $errfile";
            $str .=", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
            $str .= "Aborting...<br />\n";
            break;

        case E_USER_WARNING:
            $str .= "<b>My WARNING</b> [$errno] $errstr<br />\n";
            break;

        case E_USER_NOTICE:
            $str .= "<b>My NOTICE</b> [$errno] $errstr<br />\n";
            break;

        default:
            $str .= "Unknown error type: [$errno] $errstr<br />\n";
            $str .= "$errfile: [$errline]<br />\n";
            break;
    }

    $eh->log($str)->send();

    /* Don't execute PHP internal error handler */
    return true;
}

date_default_timezone_set('Europe/Vienna');
set_exception_handler(function($e)
{
    /**
     * @var \Exception $e
     */
    echo $e->getMessage();
    echo $e->getTraceAsString();
});

try
{
    $dispatcher = \chilimatic\lib\Di\ClosureFactory::getInstance(
        realpath(APPLICATION_PATH . '/app/config/serviceCollection.php')
    );

    /**
     * Create the config
     */
    $config = $dispatcher->get('config', [
        'type' => 'File',
        \chilimatic\lib\Config\File::CONFIG_PATH_INDEX => APPLICATION_PATH . '/app/config/'
    ]);

    $dispatcher->set('active-config', function($setting = []) use ($config) {
        return $config;
    });

    // important ! it has to be set after the active config has been created
    set_error_handler('myErrorHandler');

    /**
     * Set default timezone based on the config
     */
    date_default_timezone_set((string) $config->get('default_timezone'));

    if (!$config->get((string) 'document_root')) {
        $config->set((string) 'document_root', (string) APPLICATION_PATH);
    }

    $config->set('app_root', (string) $config->get('document_root') . (string) "/app");
    $config->set('lib_root', (string) $config->get('document_root') . (string) $config->get('lib_dir' ));

    if (!isset($_SERVER['SHELL']))
    {
        /**
         * set the current protocol SSL or normal
         */
        $config->set('protocol',(string) ($_SERVER ['SERVER_PORT'] == '443') ? 'https://' : 'http://');

        /**
         * set the current default url
         */
        $config->set('base_url', (string) $config->get('protocol') . (string) $_SERVER ['HTTP_HOST']);
    }

    $dispatcher->set('db', function() use ($dispatcher) {
        $config = $dispatcher->get('config');

        $mysqlStorage = new MySQLConnectionStorage();
        $mysqlConnectionSettings = new MySQLConnectionSettings(
            $config->get('mysql_db_host'),
            $config->get('mysql_db_user'),
            $config->get('mysql_db_password'),
            $config->get('mysql_db_name'),
            null
        );

        $mysqlConnection = new MySQLConnection(
            $mysqlConnectionSettings,
            MySQLConnection::CONNECTION_PDO
        );

        $mysqlStorage->addConnection($mysqlConnection);
        return $mysqlStorage;
    });

    $dispatcher->set('entity-manager', function() use ($dispatcher) {
        $mysqlStorage = $dispatcher->get('db');
        
        $master = $mysqlStorage->getConnectionByPosition(0);
        $queryBuilder = $dispatcher->get(
            'query-builder',
            [
                'db' => new MySQL($master)
            ]
        );

        $em = new EntityManager(
            new MySQL($master),
            $queryBuilder
        );
        return $em;
    });

    $dispatcher->set('view', function() use ($dispatcher, $config) {
        $viewClass = $config->get('default-view-class');
        return new $viewClass();
    });

    /**
     * remove remaining "free" variables
     */
    unset ($filename, $include_root, $master_param, $param, $cfg_include);
}
catch ( Exception $e ) {
    throw $e;
}
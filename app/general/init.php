<?php

date_default_timezone_set('Europe/Vienna');

set_exception_handler(function($e)
{
    echo $e->getMessage();
    echo $e->getTraceAsString();
});

try
{
    $dispatcher = \chilimatic\lib\di\ClosureFactory::getInstance(
        realpath(APPLICATION_PATH . '/app/config/serviceCollection.php')
    );

    /**
     * Create the config
     */
    $config = $dispatcher->get('config', [
        'type' => 'File',
        \chilimatic\lib\config\File::CONFIG_PATH_INDEX => APPLICATION_PATH . '/app/config/'
    ]);

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

        $mysqlStorage = new \chilimatic\lib\database\sql\mysql\connection\MySQLConnectionStorage();
        $mysqlConnectionSettings = new \chilimatic\lib\database\sql\mysql\connection\MySQLConnectionSettings(
            $config->get('mysql_db_host'),
            $config->get('mysql_db_user'),
            $config->get('mysql_db_password'),
            $config->get('mysql_db_name'),
            null
        );

        $mysqlConnection = new \chilimatic\lib\database\sql\mysql\connection\MySQLConnection(
            $mysqlConnectionSettings,
            \chilimatic\lib\database\sql\mysql\connection\MySQLConnection::CONNECTION_PDO
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
                'db' => new \chilimatic\lib\database\sql\mysql\MySQL($master)
            ]
        );

        $em = new \chilimatic\lib\database\sql\orm\EntityManager(
            new \chilimatic\lib\database\sql\mysql\MySQL($master),
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
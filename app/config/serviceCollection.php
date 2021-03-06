<?php

/**
 * Service Collection for the application
 */
    return
    [
        'config' => function($setting = []) {
            return \chilimatic\lib\config\Config::getInstance($setting);
        },
        'view' => function($setting = []) {
            return new \chilimatic\lib\view\PHtml();
        },
        'db' => function($setting = []) {
            return new PDO($setting['dns']);
        },
        'request-handler' => function($setting = []) {
            return \chilimatic\lib\request\Handler::getInstance();
        },
        'application-handler' => function($setting = []) {
            return new chilimatic\lib\handler\HTTPHandler($setting);
        },
        'routing' => function($setting = []) {
            return new \chilimatic\lib\route\Router($setting['type']);
        },
        'session' => function($setting = []){
            return new chilimatic\lib\session\handler\Session(
                chilimatic\lib\session\engine\Factory::make(@$setting['type'], @$setting['param'])
            );
        },
        'template-resolver' => function ($setting = []) {
            return new chilimatic\lib\view\resolver\templatePathStack($setting);
        },
        'cache' => function($setting = []) {
            return chilimatic\lib\cache\engine\CacheFactory::make($setting['type'], isset($setting['setting']) ? $setting['setting'] : null);
        },
        'entity-manager' => function($setting = []) {
            $connection = new \chilimatic\lib\database\sql\mysql\connection\MySQLConnection(
                new \chilimatic\lib\database\sql\mysql\connection\MySQLConnectionSettings(
                    $setting['host'],
                    $setting['username'],
                    $setting['password'],
                    isset($setting['database']) ? $setting['database'] : null,
                    isset($setting['port']) ? $setting['port'] : null,
                    isset($setting['charset']) ? $setting['charset'] : null
                )
            );

            $queryBuilder = \chilimatic\lib\di\ClosureFactory::getInstance()->get('query-builder', ['db' => new \chilimatic\lib\database\sql\mysql\Mysql($connection)]);

            $em = new \chilimatic\lib\database\sql\orm\EntityManager(
                new \chilimatic\lib\database\sql\mysql\Mysql($connection),
                $queryBuilder
            );

            return $em;
        },
        'query-builder' => function($setting = [])
        {
            $config = \chilimatic\lib\di\ClosureFactory::getInstance()->get('config');

            $cacheType = empty($setting['cache']['type']) ? $config->get('query_builder_cache') : $setting['cache']['type'];
            $cacheSettings = empty($setting['cache']['setting']) ? $config->get('query_builder_cache_setting') : $setting['cache']['setting'];

            $db = empty($setting['db']) ? $this->get('db') : $setting['db'];

            $queryBuilder = new \chilimatic\lib\database\sql\mysql\querybuilder\MySQLQueryBuilder(
                \chilimatic\lib\di\ClosureFactory::getInstance()->get('cache',
                    [
                        'type' => $cacheType,
                        'setting' => $cacheSettings
                    ]
                ),
                $db
            );
            return $queryBuilder;
        },
        'error-handler' => function($setting = []) {
            return new \chilimatic\lib\error\Handler(
                new \chilimatic\lib\log\client\PrintOutWebTemplate()
            );
        },
        'authentication-service' => function($setting = []) {
            return new \timetracker\app\module\user\service\Authentification();
        },
        'log' => function($setting = []) {
            return new \chilimatic\lib\log\client\ToFile(
                new \chilimatic\lib\formatter\Log()
            );
        },
        'error-log' => function($setting = []) {
            /**
             * @var \chilimatic\lib\log\client\ToFile $logger
             */
            $logger = \chilimatic\lib\di\ClosureFactory::getInstance()->get('log');
            $config  = \chilimatic\lib\di\ClosureFactory::getInstance()->get('config');

            $logger->setTargetFile(
                $config->get('error_log_path')
                . DIRECTORY_SEPARATOR
                . "error-"
                . date('Y-m-d')
                . ".log"
            );

            return $logger;
        }
    ];
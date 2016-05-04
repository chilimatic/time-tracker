<?php

/**
 * Service Collection for the application
 */
use chilimatic\lib\Cache\Engine\CacheFactory;
use chilimatic\lib\Database\Sql\Mysql\Connection\MySQLConnection;
use chilimatic\lib\Database\Sql\Mysql\Connection\MySQLConnectionSettings;
use chilimatic\lib\Database\Sql\Mysql\MySQL;
use chilimatic\lib\Database\Sql\Mysql\Querybuilder\MySQLQueryBuilder;
use chilimatic\lib\Database\Sql\Orm\EntityManager;
use chilimatic\lib\Di\ClosureFactory;
use chilimatic\lib\error\Handler;
use chilimatic\lib\formatter\Log;
use chilimatic\lib\log\client\PrintOutWebTemplate;
use chilimatic\lib\log\client\ToFile;

return
    [
        'config' => function($setting = []) {
            return \chilimatic\lib\config\Config::getInstance($setting);
        },
        'view' => function($setting = []) {
            return new \chilimatic\lib\view\PHtml();
        },
        'db' => function($setting = []) {
            if (!isset($setting['dns'])) {
                throw new RuntimeException('Connection string is missing');
            }

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
            return CacheFactory::make($setting['type'], isset($setting['setting']) ? $setting['setting'] : null);
        },
        'entity-manager' => function($setting = []) {
            $connection = new MySQLConnection(
                new MySQLConnectionSettings(
                    $setting['host'],
                    $setting['username'],
                    $setting['password'],
                    isset($setting['database']) ? $setting['database'] : null,
                    isset($setting['port']) ? $setting['port'] : null,
                    isset($setting['charset']) ? $setting['charset'] : null
                )
            );

            $queryBuilder = ClosureFactory::getInstance()->get('query-builder', ['db' => new Mysql($connection)]);

            $em = new EntityManager(
                new Mysql($connection),
                $queryBuilder
            );

            return $em;
        },
        'query-builder' => function($setting = [])
        {
            $config = ClosureFactory::getInstance()->get('config');

            $cacheType = empty($setting['cache']['type']) ? $config->get('query_builder_cache') : $setting['cache']['type'];
            $cacheSettings = empty($setting['cache']['setting']) ? $config->get('query_builder_cache_setting') : $setting['cache']['setting'];

            $db = empty($setting['db']) ? $this->get('db') : $setting['db'];

            $queryBuilder = new MySQLQueryBuilder(
                ClosureFactory::getInstance()->get('cache',
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
            if (!empty($setting['debug'])) {
                $client = new PrintOutWebTemplate();
            } else {
                $config = ClosureFactory::getInstance()->get('config');
                $client = new ToFile();
                $client->setTargetFile(
                    $config->get('error_log_path') . DIRECTORY_SEPARATOR . 'error' . DIRECTORY_SEPARATOR . date('Y-m-d')
                );
            }
            return new Handler($client);
        },
        'authentication-service' => function($setting = []) {
            return new \timetracker\app\module\user\service\Authentification();
        },
        'log' => function($setting = []) {
            return new ToFile(
                new Log()
            );
        },
        'error-log' => function($setting = []) {
            /**
             * @var ToFile $logger
             */
            $logger = ClosureFactory::getInstance()->get('log');
            $config  = ClosureFactory::getInstance()->get('config');

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
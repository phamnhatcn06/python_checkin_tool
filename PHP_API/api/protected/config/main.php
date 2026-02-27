<?php
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'Check-in API',

    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
    ),

    'modules' => array(
        'admin',
        'frontend',
    ),

    // Set default route to frontend module
    'defaultController' => 'frontend',

    'components' => array(
        'user' => array(
            'allowAutoLogin' => true,
        ),
        'db' => array(
            'connectionString' => 'mysql:host=127.0.0.1;dbname=checkin',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '123456a@',
            'charset' => 'utf8mb4',
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false, // Set to false to hide index.php
            'rules' => array(
                // Admin module routes
                'admin' => 'admin/default/index',
                'admin/login' => 'admin/login/index',
                'admin/logout' => 'admin/login/logout',
                'admin/<controller:\w+>/<action:\w+>' => 'admin/<controller>/<action>',

                // Frontend module routes
                'frontend' => 'frontend/default/index',
                'frontend/<controller:\w+>/<action:\w+>' => 'frontend/<controller>/<action>',

                // API routes
                'api/<action:\w+>' => 'api/<action>',

                // General rules
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ),
        ),
    ),

);

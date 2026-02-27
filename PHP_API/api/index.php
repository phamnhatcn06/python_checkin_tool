<?php
// Define path to Yii framework. 
// Downloaded via git clone yiisoft/yii
$yii = dirname(__FILE__) . '/../framework/framework/yii.php';
$config = dirname(__FILE__) . '/protected/config/main.php';
// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG', true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

if (file_exists($yii)) {
    require_once($yii);
    Yii::createWebApplication($config)->run();
} else {
    die("Yii framework not found at: " . $yii . ". Please update the path in index.php");
}

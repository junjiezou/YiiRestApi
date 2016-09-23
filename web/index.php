<?php
@include_once __DIR__ . '/../config/define-local.php';
// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
if(YII_DEBUG)
{
	ini_set('display_errors','On');
	error_reporting(E_ALL);
}
error_reporting(E_ALL^E_NOTICE^E_WARNING);

defined('YII_ENV') or define('YII_ENV', 'dev'); // prod

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../config/bootstrap.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../config/main.php')
);

(new yii\web\Application($config))->run();

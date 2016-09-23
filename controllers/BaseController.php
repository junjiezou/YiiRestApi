<?php
/**
 * api 的基类，方便对一些公共方法进行实现
 * 
 */
namespace app\controllers;
use yii;
use yii\rest\ActiveController;


class BaseController extends ActiveController
{
    public $modelClass = 'common\models\member\Member';
    
}
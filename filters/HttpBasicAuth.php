<?php
/**
 * Author : <caoxiaoming>
 * Email : <xiaoming.cao@dfs168.com>
 * Project : <wego-group.dfs168>
 * CreateTime : <2016/8/16 11:32>
 */

namespace app\filters;
use Yii;
use app\components\Auth;

class HttpBasicAuth extends \yii\filters\auth\HttpBasicAuth
{
    public function authenticate($user, $request, $response)
    {
        $uid =  array_merge(Yii::$app->request->get(),Yii::$app->request->post())['uid'];
        $auth = new Auth($uid);
        $auth->checkToken();
        \Yii::$app->user->login(\app\models\User::findIdentity($uid));
        return true;
    }
}
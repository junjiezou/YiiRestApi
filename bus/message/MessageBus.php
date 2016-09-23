<?php
/**
 * Author : <caoxiaoming>
 * Email : <xiaoming.cao@dfs168.com>
 * Project : <wego-group.dfs168>
 * CreateTime : <2016/8/22 10:30>
 */
namespace app\bus\message;
use yii;
use common\models\message\AppPush;
class MessageBus extends \common\components\Business
{
    public static $messagePageLimit = 5;
    public static function getPushMessageList($params){
        $member_id =  Yii::$app->user->identity->member_id;
        //$member_id = 1494;
        $offset = ($params['currentPage']-1) * self::$messagePageLimit;
        $query = AppPush::find()->where(['member_id'=>$member_id])->orderBy('created_at desc');

        //总条目数与分页数据
        $cQuery = clone $query;
        $countItem = $cQuery->count();
        $page = [
            'countPage'=>ceil($countItem/self::$messagePageLimit),
            'currentPage'=>$params['currentPage'],
            'countItem' => $countItem,
            'pageItme' => self::$messagePageLimit
        ];

        //如果没有数据则返回
        if($countItem < 1)  return ['status'=>'success','page'=>$page,'list'=>[]];

        //查询数据
        $list = $query->orderBy('app_push_time desc')->offset($offset)->limit(self::$messagePageLimit)->asArray()->all();
        return ['status'=>'success','page'=>$page,'list'=>$list];
    }
}
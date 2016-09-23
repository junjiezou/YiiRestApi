<?php
/**
 * Author : <caoxiaoming>
 * Email : <xiaoming.cao@dfs168.com>
 * Project : <wego-group.dfs168>
 * CreateTime : <2016/8/19 17:30>
 */
namespace app\bus\order;
use yii;
use common\models\order\Order;
class OrderBus extends \common\components\Business
{
    public static $orderPageLimit = 3;
    /*
     *订单列表
     * author : caoxiaoming
    */
    public static function getOrderList($params){
        $member_id =  Yii::$app->user->identity->member_id;
        $offset = ($params['currentPage']-1) * self::$orderPageLimit;
        $query = Order::find()->where(['OR','buyer_id='.$member_id,'really_order_member_id='.$member_id]);

        //总条目数与分页数据
        $cQuery = clone $query;
        $countItem = $cQuery->count();
        $page = [
            'countPage'=>ceil($countItem/self::$orderPageLimit),
            'currentPage'=>$params['currentPage'],
            'countItem' => $countItem,
            'pageItme' => self::$orderPageLimit
        ];

        //如果没有数据则返回
        if($countItem < 1)  return ['status'=>'success','page'=>$page,'list'=>[]];

        //查询数据
        $query = $query->orderBy('add_time desc')->with('store')->with('orderGoods')->with('rebater');
        $list = $query->offset($offset)->limit(self::$orderPageLimit)->asArray()->all();
        return ['status'=>'success','page'=>$page,'list'=>$list];
    }

}
<?php
/**
 * Author : <caoxiaoming>
 * Email : <xiaoming.cao@dfs168.com>
 * Project : <wego-group.dfs168>
 * CreateTime : <2016/8/18 16:01>
 */
namespace app\bus\appOrder;
use yii;
use common\models\order\AppOrder;
use app\bus\member\MemberBus;

class AppOrderBus extends \common\components\Business
{
    //少于3的都是未完成订单
    const UNFINISHED_ORDER_STATE = 3;

    /**
     *获取未完成的订单
     *@author caoxiaoming
     */
    public function getUnfinishedOrder(){
        $phone = Yii::$app->user->identity->member_phone;
        $query = AppOrder::find()->andWhere(['app_order_member_phone'=>$phone])->andWhere(['<','app_order_state',self::UNFINISHED_ORDER_STATE]);
        return $query->one();
    }

    /**
     *创建订单
     *@author caoxiaoming
     */
    public function createAppOrder($params){
        $appOrder = new AppOrder();
        $appOrder->app_order_member_phone= Yii::$app->user->identity->member_phone;
        $appOrder->app_order_account= Yii::$app->user->identity->member_phone;
        $appOrder->app_order_sn = 'AO'.time().mt_rand(1000, 9999);
        $appOrder->app_order_state = 0;
        $appOrder->app_order_type = 0;
        $appOrder->app_order_time=time();
        if($appOrder->save()){
            return ['status'=>'success','appOrder'=>$appOrder];
        }else{
            return ['status'=>'error','message'=>'订单创建失败'];
        }
    }

    /**
     *删除订单
     *@author caoxiaoming
     */
    public function deleteAppOrder($params){
        $phone = Yii::$app->user->identity->member_phone;
        $query = AppOrder::find()->andWhere(['app_order_member_phone'=>$phone])->andWhere(['<','app_order_state',self::UNFINISHED_ORDER_STATE])->one();
        if(!$query) return ['status'=>'success','message'=>'success'];
        $query->app_order_state=4;
        $result = $query->update();
        if($result===false){
            return ['status'=>'error','message'=>'删除订单失败'];
        }else{
            return ['status'=>'success','message'=>'success'];
        }
    }

    /**
     * 在app下单后，根据orderID，获取app订单的代购员信息
     * @author caoxiaoming
     */
    public function getAppOrderRebate($params){
        $order = AppOrder::find()->where(['app_order_id'=>$params['id'],'app_order_member_phone' => Yii::$app->user->identity->member_phone])->one();
        if(!$order) return ['status'=>'error','message'=>'订单不存在'];
        if(!$order->app_order_rebate) return ['status'=>'error','message'=>'该订单还未指定代购员'];
        $member = (new MemberBus())->findByMemberId($order->app_order_rebate);
        return ['status'=>'success','member'=>$member];
    }
}
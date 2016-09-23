<?php
/**
 * Author : <caoxiaoming>
 * Email : <xiaoming.cao@dfs168.com>
 * Project : <wego-group.dfs168>
 * CreateTime : <2016/8/15 16:01>
 */
namespace app\bus\buy;
use yii;
use app\bus\member\MemberBus;
use app\bus\appOrder\AppOrderBus;
use app\components\mail\SendMail;

class BuyBus extends \common\components\Business
{
    /**
     *提交订单
     *@author caoxiaoming
     */
    public function submitOrder($params){
        //检查是否有未完成的订单
        $result = $this->checkUnfinishedOrder();
        if($result['status']=='error') return $result;

        $appOrder = new AppOrderBus();
        $result = $appOrder->createAppOrder($params);
        if($result['status']=='success') $this->sendMail();
        return $result;
    }

    /**
     *检查是否存在未处理完的订单
     *@author caoxiaoming
     */
    public function checkUnfinishedOrder(){
        $appOrder = new AppOrderBus();
        $order = $appOrder->getUnfinishedOrder();
        if($order){
            return ['status'=>'error','message'=>'有未完成的订单，是否删除'];
        }else{
            return ['status'=>'success','message'=>'没有未完成的订单，可以下单'];
        }
    }

    /**
     * app下单后，发送邮件通知营运，为订单指定代购员
     * @author caoxiaoming
     */
    public function sendMail(){
        $Subject = '丰收农资购APP新订单';
        $Body = "丰收农资购抢单消息：手机号码:". \Yii::$app->user->identity->member_phone ."提交了买农资需求，请立即安排代购员接单";;
        SendMail::sendMail($Subject,$Body);
    }
}
<?php
/**
 * Author : <caoxiaoming>
 * Email : <xiaoming.cao@dfs168.com>
 * Project : <wego-group.dfs168>
 * CreateTime : <2016/8/19 10:30>
 */
namespace app\bus\signin;
use yii;
use common\models\points\PointsLog;
use app\bus\member\MemberBus;
use yii\base\Exception;
class SigninBus extends \common\components\Business
{
    /*
     * 查询今天是否已经签到
     */
    public static function isSign(){
        //如果没有开启签到，则默认返回已经签到
        if(!Yii::$app->params['signIn']['enableSign']) return ['status'=>'success','isSign'=>'Y'];
        $signTime = PointsLog::find()->where(['pl_memberid'=>Yii::$app->user->identity->member_id,'pl_stage'=>'appSign'])
            ->select('pl_addtime')->orderBy('pl_addtime desc')->asArray()->one();

        //如果没有查询到签到的时间，说明没有签到
        if(!$signTime) return ['status'=>'success','isSign'=>'N'];

        //如果最后的签到时间不是今天，则没有签到
        if(date('Y-m-d',intval($signTime['pl_addtime'])) != date('Y-m-d')) return ['status'=>'success','isSign'=>'N'];

        //已经签到
        return ['status'=>'success','isSign'=>'Y'];
    }

    /*
     * 用户签到
     */
    public static function userSign(){
        $tmp = self::isSign();
        if($tmp['isSign']=='Y') return ['status'=>'error','message'=>'您今天已经签到过了'];
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            //签到记录
            $tmp = self::createSignLog();
            //修改用户积分
            $mem = new MemberBus();
            $tmp = $mem->updateUserPoint($tmp['points']);
            $transaction->commit();
            return ['status'=>'success','points'=>$tmp['points']];
        } catch (Exception $e) {
            $transaction->rollBack();
            return ['status'=>'error','message'=>$e->getMessage()];
        }
    }

    /*
     * 写入签到记录
     */
    public static function createSignLog(){
        $loginPoint = Yii::$app->params['signIn']['loginPoint'];
        $increasePoint = Yii::$app->params['signIn']['increasePoint'];
        $member_login_days = Yii::$app->user->identity->member_login_days;
        $pl_points = (($member_login_days-1)*$increasePoint)+$loginPoint;
        $pLog = new PointsLog();
        $pLog->pl_memberid = Yii::$app->user->identity->member_id;
        $pLog->pl_membername = Yii::$app->user->identity->member_name;
        $pLog->pl_points = $pl_points;
        $pLog->pl_desc = '签到';
        $pLog->pl_stage = 'appSign';
        $pLog->pl_addtime = time();
        if(!$pLog->save()){
            throw new Exception("写入签到异常");
        }
        return ['status'=>'success','points'=>$pl_points];
    }
}
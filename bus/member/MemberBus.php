<?php
/**
 * Author : <caoxiaoming>
 * Email : <xiaoming.cao@dfs168.com>
 * Project : <wego-group.dfs168>
 * CreateTime : <2016/8/10 14:30>
 */
namespace app\bus\member;
use yii;
use yii\base\Exception;
use common\models\member\Member;
class MemberBus extends \common\bus\member\MemberBus
{
    public function appLogin($phone)
    {
        $member = $this->findByPhone($phone);
        //如果用户非真,则去注册
        if(!$member) {
            $result = $this->appRegister($phone);
            if($result['status']=='error') return $result;
            $member = $result['member'];
        }

        //上次登录的时间，如果非真，就等于0
        $oldLoginTime = $member->member_login_time ? : 0;

        //如果上次登录和本次不是同一天，则需计算连续登录的天数
        if(date('Y-m-d',$oldLoginTime) != date('Y-m-d',time())){
            $last_day_begin = strtotime(date('Y-m-d',time())) - 24*3600; //获取昨天00:00的时间戳
            //如果登录发生在昨天之前，将连续登录次数设置为1，否则累加
            if( $last_day_begin < $member->member_login_time ){
                $member->member_login_days += 1;
            }else{
                $member->member_login_days = 1 ;
            }
        }

        //记录上次的登录时间，如果非真则为0
        $member->member_old_login_time = $member->member_login_time ? : 0;
        $member->member_login_time=time();
        $member->member_login_num += 1;
        $member->save(false);
        return ['status'=>'success','member'=>$member];
    }

    /*
        * app简易注册
    */
    public function appRegister($phone)
    {
        $member = new Member();
        $member->member_phone= $phone;
        $member->member_name= $phone;
        $member->register_method =1;
        $member->member_time = date('Y-m-d',time());
        if(!$member->save(false)) return ['status'=>'error','message'=>'创建用户失败'];
        return ['status'=>'success','member'=>$member];
    }

    /*
        *根据手机号拿取用户信息
    */
    public function findByPhone($phone)
    {
        return Member::find()->where(['member_phone' => $phone])->one();
    }


    /*
        *根据ID拿取用户信息
    */
    public function findByMemberId($member_id)
    {
        return Member::find()->where(['member_id' => $member_id])->one();
    }

    /**
    * 修改用户积分
    * @author:caoxiaoming
    */
    public function updateUserPoint($points){
        $member = Member::findOne(\Yii::$app->user->identity->member_id);
        $member->member_points += $points;
        if(!$member->save(false)){
            throw new Exception("增加丰收币异常");
        }
        return ['status'=>'success','points'=>$member->member_points];
    }


    /**
    * 修改用户头像
    * @author:caoxiaoming
    */
    public function updateUserPic($pic){
        $member = Member::findOne(\Yii::$app->user->identity->member_id);
        $member->member_avatar = $pic;
        if(!$member->save(false)) return ['status'=>'error','message'=>'修改用户头像失败'];
        return ['status'=>'success','pic'=>$member->member_avatar];
    }


    /**
     * 修改用户信息
     * @author:caoxiaoming
     */
    public function updateUserInfo($data){
        $member = Member::findOne(\Yii::$app->user->identity->member_id);
        $member->member_sex = $data['member_sex'];
        $member->member_truename = $data['member_truename'];
        if(!$member->save(false)) return ['status'=>'error','message'=>'修改失败'];
        return ['status'=>'success','message'=>'修改成功'];
    }

    /**
     * 设置可用的摇一摇次数
     * @author:caoxiaoming
     */
    public static function setLotteryNum(){
        $member = Member::findOne(\Yii::$app->user->identity->member_id);
        $time = $member->app_prize_time > 0 ? $member->app_prize_time : 0;
        if(date('Y-m-d')==date('Y-m-d',$time)) {
            $member->app_prize_num += 1;
        }else{
            $member->app_prize_num = 1;
        }
        $member->app_prize_time=time();
        if(!$member->save(false)){
            throw new Exception("修改抽奖次数异常");
        }
        return ['status'=>'success','num'=>$member->app_prize_num];
    }


}
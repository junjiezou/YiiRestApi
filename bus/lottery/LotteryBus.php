<?php
/**
 * Author : <caoxiaoming>
 * Email : <xiaoming.cao@dfs168.com>
 * Project : <wego-group.dfs168>
 * CreateTime : <2016/7/18 10:30>
 */
namespace app\bus\lottery;
use yii;
use yii\base\Exception;
use common\models\prize\AppPrize;
use common\models\prize\AppPrizeRecord;
use app\bus\member\MemberBus;
class LotteryBus extends \common\components\Business
{
    //每天最多能抽奖的次数
    public static $lotteryMaxNum = 3;

    //默认的中奖概率 1/10000
    public static $lotteryChance = 10000;

    /**
     * 抽奖的商品列表
     * @author:caoxiaoming
     */
    public static function getPrizeGoods(){
        $list = AppPrize::find()->andWhere(['is_deleted'=>0])->andWhere(['>','app_prize_storage',0])->limit(4)->asArray()->all();
        $list = $list ? : [];
        return ['status'=>'success','list'=>$list];
    }

    /**
     * 获取可用的摇一摇次数
     * @author:caoxiaoming
     */
    public static function getLotteryNun(){
        $num = Yii::$app->user->identity->app_prize_num;
        $time = Yii::$app->user->identity->app_prize_time ? : 0;
        //默认可抽奖的次数
        $defaultNum = self::$lotteryMaxNum;
        //如果最后的抽奖时间不是当前，则认为当天还未抽奖，否则减去已经抽奖的次数
        if(date('Y-m-d')==date('Y-m-d',$time)) {
            $defaultNum -= $num;
        }
        return ['status'=>'success','num'=>$defaultNum];
    }

    /**
     * 摇一摇抽奖
     * @author:caoxiaoming
     */
    public static function lotteryDraw(){
        $Num = self::getLotteryNun();
        if($Num['num'] < 1) return ['status'=>'error','message'=>'您的抽奖机会已用完，请明天再来'];
        //查询奖品
        $lotteryGoods = AppPrize::find()->andWhere(['is_deleted'=>0])->andWhere(['>','app_prize_storage',0])->asArray()->all();
        if(!$lotteryGoods)  return ['status'=>'error','message'=>'该功能暂未开通'];
        //抽奖
        $lotteryResult = self::lottery($lotteryGoods);
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            //修改用户抽奖次数
            $mem = new MemberBus();
            $tmpNum = $mem->setLotteryNum();
            //可用摇一摇次数
            $num = self::$lotteryMaxNum - $tmpNum['num'];
            //中奖标记
            $draw = 'N';
            //判断是否中奖，和是否有库存
            if($lotteryResult['goods'] and ($lotteryResult['goods']['app_prize_storage'] > 0)){
                //写入中奖记录
                self::insertLotteryRecord($lotteryResult['goods']);
                //减少奖品库存
                self::setLotteryGoodsStorage($lotteryResult['goods']['app_prize_id']);
                //如果是积分，给用户增加积分
                if($lotteryResult['goods']['app_prize_type']==1) $mem->updateUserPoint($lotteryResult['goods']['app_prize_points']);
                $draw = 'Y';
            }
            $transaction->commit();
            return ['status'=>'success','goods'=>$lotteryResult['goods'],'draw'=>$draw,'num'=>$num];
        } catch (Exception $e) {
            $transaction->rollBack();
            return ['status'=>'error','message'=>$e->getMessage()];
        }
    }

    /**
     * 中奖算法
     * @author:caoxiaoming
     */
    public static function lottery($lotteryGoods){
        shuffle($lotteryGoods);
        $defaultGoods = [];
        foreach($lotteryGoods as $value){
            $max = $value['app_prize_chance'] > 0 ? $value['app_prize_chance'] : self::$lotteryChance;
            $rand = mt_rand(1,$max);
            if($rand==$max){
                $defaultGoods = $value;
                break;
            }
        }
        return ['status'=>'success','goods'=>$defaultGoods];
    }


    /**
     * 写入中奖记录
     * @author:caoxiaoming
     */
    public static function insertLotteryRecord($prize){
        $record = new AppPrizeRecord();
        $record->app_prize_id = $prize['app_prize_id'];
        $record->member_id = Yii::$app->user->identity->member_id;
        $record->member_phone = Yii::$app->user->identity->member_phone;
        $record->app_prize_record_state = $prize['app_prize_type']==1 ? 1 :0;
        $record->app_prize_record_time = time();
        if(!$record->save(false)) throw new Exception("写入中奖记录异常");
        return ['status'=>'success','message'=>'success'];
    }


    /**
     * 中奖后，减少奖品库存
     * @author:caoxiaoming
     */
    public static function setLotteryGoodsStorage($app_prize_id){
        $appPrize =  AppPrize::findOne($app_prize_id);
        $appPrize->app_prize_storage -= 1;
        if(!$appPrize->save(false)) throw new Exception("减少奖品库存异常");
        return ['status'=>'success','message'=>'success'];
    }

    /**
     * 中奖记录
     * @author:caoxiaoming
     */
    public static function getLotteryRecord($memberId=null){
        $query = AppPrizeRecord::find();
        if($memberId) $query->andWhere(['member_id'=>$memberId]);
        $tmpList = $query->with('prize')->OrderBy('app_prize_record_time desc')->limit(10)->asArray()->all();
        $list = [];
        if($tmpList){
            foreach($tmpList as $k=>$v){
                $list[$k]['app_prize_record_id'] = $v['app_prize_record_id'];
                $list[$k]['member_id'] = $v['member_id'];
                $list[$k]['member_phone'] = $v['member_phone'];
                $list[$k]['app_prize_record_state'] = $v['app_prize_record_state'];
                $list[$k]['app_prize_record_time'] = $v['app_prize_record_time'];
                $list[$k]['app_prize_name'] = $v['prize']['app_prize_name'];
                $list[$k]['app_prize_img'] = Yii::$app->params['appPrizeImagePath'].'/'.$v['prize']['app_prize_img'];
                $list[$k]['app_prize_price'] = $v['prize']['app_prize_price'];
                $list[$k]['num']  = $v['prize']['app_prize_type']==1 ? $v['prize']['app_prize_points'] : 1;
            }
        }
        return ['status'=>'success','list'=>$list];
    }
}
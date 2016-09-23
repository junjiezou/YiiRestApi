<?php
/**
 * Author : <caoxiaoming>
 * Email : <xiaoming.cao@dfs168.com>
 * Project : <wego-group.dfs168>
 * CreateTime : <2016/7/18 10:30>
 */
namespace app\bus\address;
use yii;
use common\models\address\Address;
class AddressBus extends \common\components\Business
{
    /**
     * 添加地址
     * @author caoxiaoming
     */
    public static function addAddress($data)
    {
        $model = new Address;
        $model->true_name = $data['true_name'];
        $model->mob_phone = $data['mob_phone'];
        $model->address = $data['address'];
        $model->member_id = Yii::$app->user->id;
        $model->area_id = $data['area_id'];
        $model->city_id = $data['city_id'];
        $model->area_info = $data['area_info'];
        if ($model->save()) {
            return ['status'=>'success','address'=>$model];
        } else {
            $message = '添加地址失败';
            if($model->hasErrors()) $message = current($model->getErrors())[0];
            return ['status'=>'error','message'=>$message];
        }
    }

    /**
     * @param 删除地址信息，只支持逐个删除 用address_id来找记录，然后执行删除操作
     * @author caoxiaoming
     */
    public static function deleteAddress($id)
    {
        $model = Address::find()->where(['address_id'=>$id,'member_id'=>Yii::$app->user->id])->one();
        if(!$model) return ['status'=>'error','message'=>'地址不存在'];
        if($model->delete()){
            return ['status'=>'success','message'=>'删除地址成功'];
        }else{
            return ['status'=>'error','message'=>'删除地址失败'];
        }
    }

    /**
     * 获取页面数据，校验，更新，返回结果。
     * @author caoxiaoming
     */
    public static function updateAddress($data)
    {
        $model = Address::find()->where(['address_id'=>$data['id'],'member_id'=>Yii::$app->user->id])->one();
        $model->true_name = $data['true_name'];
        $model->mob_phone = $data['mob_phone'];
        $model->address = $data['address'];
        $model->member_id = Yii::$app->user->id;
        $model->area_id = $data['area_id'];
        $model->city_id = $data['city_id'];
        $model->area_info = $data['area_info'];
        if ($model->save()) {
            return ['status'=>'success','address'=>$model];
        } else {
            return ['status'=>'error','message'=>'修改地址失败'];
        }
    }

    /**
     * 获取用户地址列表
     * @author caoxiaoming
     */
    public static function addressList(){
        $list = Address::find()->andWhere(['member_id'=>Yii::$app->user->id])->asArray()->all();
        return ['status'=>'success','list'=> $list ? : []];
    }

    /**
     * 获取地址详细信息
     * @author caoxiaoming
     */
    public static function getAddressDetail($id){
        $model = Address::find()->where(['address_id'=>$id,'member_id'=>Yii::$app->user->id])->asArray()->one();
        if(!$model) return ['status'=>'error','message'=>'地址不存在'];
        return ['status'=>'success','address'=> $model];
    }

    /**
     * 设置默认值
     * @author caoxiaoming
     */
    public static function setDefaultAddress($id)
    {
        $model = Address::find()->where(['address_id'=>$id,'member_id'=>Yii::$app->user->id])->one();
        if(!$model) return ['status'=>'error','message'=>'地址不存在'];
        Address::updateAll(['is_default'=>'0'],['member_id' => Yii::$app->user->id,'is_default'=>'1']);
        $model->is_default=1;
        if(!$model->save()) return ['status'=>'error','message'=> '设置失败'];
        return ['status'=>'success','message'=> '设置成功'];
    }
}
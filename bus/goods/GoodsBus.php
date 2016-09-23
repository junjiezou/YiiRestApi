<?php
/**
 * Author : <caoxiaoming>
 * Email : <xiaoming.cao@dfs168.com>
 * Project : <wego-group.dfs168>
 * CreateTime : <2016/7/15 16:01>
 */
namespace app\bus\goods;
use yii;
use common\models\goods\Goods;
use yii\data\Pagination;
use yii\data\ActiveDataProvider;
use common\models\rebate\RebateAddress;
use common\bus\site\SiteBus;

class GoodsBus extends \common\bus\goods\GoodsBus
{
    public static function getThumb($goods_image,$size=240){
        if(!isset($goods_image) || empty($goods_image) || !in_array($size, [60,160,240,310,1280])){
            //返回默认的缩略图商品图片
            return Yii::$app->params['goodsImageDefaultPath'].'image_'.$size.'.gif';
        }

        $arr=explode('_',$goods_image,2);//商品图片根据使用_符号做了分割，eg:店铺ID_图片名称
        if(count($arr) == 2){
            $imagePath=$arr[0].'/'.$goods_image;
            $imagePath=str_replace('.', '_' . $size . '.', $imagePath);// 缩略图的命名规则 378837878738873_size.jpg|png|gif
            return Yii::$app->params['goodsImageThumbPath'].$imagePath;
        }
        return Yii::$app->params['goodsImageDefaultPath'].'image_'.$size.'.gif';
    }
}
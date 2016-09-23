<?php
/**
 * 注册会员model
 *
 */
namespace app\models\member;

class Member extends \app\models\BaseActiveRecord
{

	const FINANCE_YES = 1; // 金融用户的标记值

	const REBATE_STATE_NO=0;//不是代购员
	const REBATE_STATE_YES=1;//是代购员
	const REBATE_STATE_APPLY=2;//申请中
	const REBATE_STATE_CLOSE=3;//已关闭

	const SERVICE_STATE_YES=1;//是服务商
	const SERVICE_STATE_NO=0;//不是服务商
	const SERVICE_STATE_AUDITING=2;//审核中
	const SERVICE_STATE_REJECT=3;//已拒绝

    const CENTER_STATE_NO=0;//不是服务中心
    const CENTER_STATE_YES=1;//是服务中心
    const CENTER_STATE_APPLY=2;//一审申请中
    const CENTER_STATE_REAPPLY=4;//二审申请中
    const CENTER_STATE_CLOSE=3;//已拒绝
	public static function tableName()
	{
		return '{{%member}}';
	}


	public function rules()
	{
        return [
//            [['member_name', 'member_passwd', 'member_email', 'member_time', 'member_login_time', 'member_old_login_time', 'rebate_goods_amount', 'rebate_get_amount', 'prize_num', 'prize_time', 'operator_rebate', 'is_service', 'service_name', 'service_add_time', 'service_refuse_text', 'service_get_amount', 'operator_service', 'service_goods_amount', 'service_areaid', 'service_detail_addr'], 'required'],
            [['member_sex', 'member_login_num', 'member_points', 'inform_allow', 'is_buy', 'is_allowtalk', 'member_state', 'member_credit', 'member_snsvisitnum', 'member_areaid', 'member_cityid', 'member_provinceid', 'member_add_land_condition', 'rebate_state', 'rebate_add_time', 'can_eidt_num', 'member_login_days', 'rebate_is_sent', 'rebate_order_num', 'app_prize_num', 'app_prize_time', 'register_method', 'prize_num', 'prize_time', 'is_service', 'service_add_time', 'service_areaid', 'jr_type', 'member_attr','is_service_center'], 'integer'],
            [['member_phone'], 'unique'],
            [['member_birthday'], 'safe'],
            [['member_qqinfo', 'member_sinainfo', 'member_privacy'], 'string'],
            [['available_predeposit', 'freeze_predeposit', 'rebate_goods_amount', 'rebate_get_amount', 'service_get_amount', 'service_goods_amount'], 'number'],
            [['member_name', 'member_avatar', 'member_promoter', 'member_promoterId', 'member_phone'], 'string', 'max' => 50],
            [['member_truename', 'member_login_ip', 'member_old_login_ip'], 'string', 'max' => 20],
            [['member_passwd', 'member_paypasswd'], 'string', 'max' => 32],
            [['member_email', 'member_qq', 'member_ww', 'member_qqopenid', 'member_sinaopenid', 'member_add_farmers_property', 'rebate_refuse_text'], 'string', 'max' => 100],
            [['member_time', 'member_login_time', 'member_old_login_time'], 'string', 'max' => 10],
            [['member_areainfo'], 'string', 'max' => 255],
            [['member_add_id_card'], 'string', 'max' => 30],
            [['member_add_species', 'member_add_agricutural_brand', 'member_add_detail_addr', 'service_detail_addr'], 'string', 'max' => 500],
            [['member_add_purchase_plan', 'rebate_goods_type', 'service_refuse_text'], 'string', 'max' => 200],
            [['operator_rebate', 'operator_service'], 'string', 'max' => 40],
            [['service_name'], 'string', 'max' => 80]
			];
	}

    public function scenarios()
    {
    	$scenarios = parent::scenarios();
    	$scenarios['wechat_login']=['member_phone','member_name','member_provinceid','member_cityid','member_areaid','member_areainfo'];
    	$scenarios['wallet']=['available_predeposit','freeze_predeposit'];
        return $scenarios;
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'member_id' => 'Member ID',
            'member_name' => '用户名',
            'member_truename' => '用户真实姓名',
            'member_avatar' => 'Member Avatar',
            'member_sex' => 'Member Sex',
            'member_birthday' => 'Member Birthday',
            'member_passwd' => 'Member Passwd',
            'member_paypasswd' => 'Member Paypasswd',
            'member_email' => 'Member Email',
            'member_qq' => 'Member Qq',
            'member_ww' => 'Member Ww',
            'member_login_num' => 'Member Login Num',
            'member_time' => 'Member Time',
            'member_login_time' => 'Member Login Time',
            'member_old_login_time' => 'Member Old Login Time',
            'member_login_ip' => 'Member Login Ip',
            'member_old_login_ip' => 'Member Old Login Ip',
            'member_qqopenid' => 'Member Qqopenid',
            'member_qqinfo' => 'Member Qqinfo',
            'member_sinaopenid' => 'Member Sinaopenid',
            'member_sinainfo' => 'Member Sinainfo',
            'member_points' => 'Member Points',
            'available_predeposit' => 'Available Predeposit',
            'freeze_predeposit' => 'Freeze Predeposit',
            'inform_allow' => 'Inform Allow',
            'is_buy' => 'Is Buy',
            'is_allowtalk' => 'Is Allowtalk',
            'member_state' => 'Member State',
            'member_credit' => 'Member Credit',
            'member_snsvisitnum' => 'Member Snsvisitnum',
            'member_areaid' => '用户所在地',
            'member_cityid' => 'Member Cityid',
            'member_provinceid' => 'Member Provinceid',
            'member_areainfo' => 'Member Areainfo',
            'member_privacy' => 'Member Privacy',
            'member_promoter' => 'Member Promoter',
            'member_promoterId' => 'Member Promoter ID',
            'member_phone' => '用户手机号码',
            'member_add_id_card' => 'Member Add Id Card',
            'member_add_species' => 'Member Add Species',
            'member_add_agricutural_brand' => 'Member Add Agricutural Brand',
            'member_add_land_condition' => 'Member Add Land Condition',
            'member_add_farmers_property' => 'Member Add Farmers Property',
            'member_add_detail_addr' => 'Member Add Detail Addr',
            'member_add_purchase_plan' => 'Member Add Purchase Plan',
            'rebate_state' => 'Rebate State',
            'rebate_add_time' => 'Rebate Add Time',
            'rebate_goods_amount' => 'Rebate Goods Amount',
            'rebate_get_amount' => 'Rebate Get Amount',
            'rebate_refuse_text' => 'Rebate Refuse Text',
            'can_eidt_num' => 'Can Eidt Num',
            'member_login_days' => 'Member Login Days',
            'rebate_goods_type' => 'Rebate Goods Type',
            'rebate_is_sent' => 'Rebate Is Sent',
            'rebate_order_num' => 'Rebate Order Num',
            'app_prize_num' => 'App Prize Num',
            'app_prize_time' => 'App Prize Time',
            'register_method' => 'Register Method',
            'prize_num' => 'Prize Num',
            'prize_time' => 'Prize Time',
            'operator_rebate' => 'Operator Rebate',
            'is_service' => 'Is Service',
            'service_name' => 'Service Name',
            'service_add_time' => 'Service Add Time',
            'service_refuse_text' => 'Service Refuse Text',
            'service_get_amount' => 'Service Get Amount',
            'operator_service' => 'Operator Service',
            'service_goods_amount' => 'Service Goods Amount',
            'service_areaid' => 'Service Areaid',
            'service_detail_addr' => 'Service Detail Addr',
            'jr_type' => 'Jr Type',
            'member_attr' => 'Member Attr',
            'is_service_center' => 'Is_service_center',
            'member_attr' => 'Member Attr',
        ];
    }
}

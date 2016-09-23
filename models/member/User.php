<?php

/**
 * 用户类，用于微信用户登录
* @author zoujunjie
*
*/
namespace app\models;

class User extends Member implements \yii\web\IdentityInterface
{

	public $authKey='dci9nml03c2c3exdSEoD';
	public $accessToken;

	/**
	 * @inheritdoc
	 */
	public static function findIdentity($id)
	{
		return static::findOne(['member_id'=>$id]);
	}

	/**
	 * @inheritdoc
	 */
	public static function findIdentityByAccessToken($token, $type = null)
	{
		return null;
	}

	/**
	 * @inheritdoc
	 */
	public function getId()
	{
		return $this->member_id;
	}

	/**
	 * @inheritdoc
	 */
	public function getAuthKey()
	{
		return $this->authKey;
	}

	/**
	 * @inheritdoc
	 */
	public function validateAuthKey($authKey)
	{
		return $this->authKey === $authKey;
	}
}

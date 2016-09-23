<?php
/**
 * 基础数据操作model
 *
 */

namespace app\models;

use yii\db\ActiveRecord;

abstract class BaseActiveRecord extends ActiveRecord
{
	// 处理一些时间字段
	public function beforeSave($insert)
	{
		$now=date('Y-m-d H:i:s');
		if($insert===true && $this->hasAttribute('create_time'))
			$this->create_time=$now;

		if($insert===true && $this->hasAttribute('add_time'))
			$this->add_time=time();

		if($this->hasAttribute('update_time'))
			$this->update_time=$now;

		return true;
	}
}

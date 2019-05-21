<?php

namespace app\models;

use Yii;

class Company extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'companies';
    }

    public function rules()
    {
        return [
            [['comp_id', 'comp_deleted'], 'integer'],
            [['comp_name', 'comp_addr'], 'required'],
            [['comp_name', 'comp_addr'], 'string', 'max' => 500],
            [['comp_phone', 'comp_email'], 'string', 'max' => 255],
            [['comp_created', 'comp_updated'], 'safe'],
            [['comp_name'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'comp_id' => 'ID',
            'comp_created' => '(created)',
            'comp_updated' => '(updated)',
            'comp_name' => 'Name',
            'comp_addr' => 'Address',
            'comp_phone' => 'Phone',
            'comp_email' => 'Email',
            'comp_deleted' => '(deleted flag)',
            
        ];
    }

	public function beforeSave($insert)
	{
		if (parent::beforeSave($insert))
		{
			if ($insert) {
				$this->comp_created = date('Y-m-d H:i:s');
			}
			
			$this->comp_updated = date('Y-m-d H:i:s');

			return true;
		}

		return false;
	}
}

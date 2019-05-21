<?php

namespace app\models;

use yii\base\Model;

class CompanyForm extends Model
{
    public $comp_id;
	public $comp_created;
	public $comp_updated;
	public $comp_name;
	public $comp_addr;
	public $comp_phone;
	public $comp_email;
	public $comp_deleted;

	public function rules()
	{
		
		$result = [
			[['comp_name', 'comp_addr'], 'required'],
// 			[['comp_phone'], 'match', 'pattern' => '/^\+7 (\([\d]{5}\) [\d]{1}|\([\d]{4}\) [\d]{2}|\([\d]{3}\) [\d]{3})-[\d]{2}-[\d]{2}$/'],
			[['comp_phone'], 'validatePhone'],
			[['comp_email'], 'email'],
			[['comp_deleted'], 'boolean'],
		];
		
		return $result;
	}

    public function attributeLabels()
    {
        return [
            'comp_name' => 'Название компании',
            'comp_addr' => 'Адрес',
            'comp_phone' => 'Телефон',
            'comp_email' => 'Электропочта',
        ];
    }

 

	public function validatePhone($attribute, $params)
	{
		$value = (string) $this->$attribute;

		if (preg_match('~^\+?[0-9]?[-0-9 ]{5,}$~', $value, $matches) == false) 
		{
			$this->addError($attribute, 'Incorrect phone number in ' . $attribute . '.');
		}
	}

}

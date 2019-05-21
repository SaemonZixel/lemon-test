<?php

namespace api\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;

class SiteController extends Controller
{
    public function actions()
    {
    }

    public function actionIndex() 
    {
		return $this->render('index', array('title' => '123'));
    }
    
    public function actionTestTarget($make = null, $target = null) 
    {
		// исключительно для удобной отладки
		if (!empty($target)) {
			\app\components\lib\SMSAndEMailTarget::$test_phone = $target;
			\app\components\lib\SMSAndEMailTarget::$test_email = $target;
		}
		
		switch ($make) {
			case 'warning': Yii::warning("Warning test!"); break;
			case 'error': Yii::error("Error test!"); break;
		}
		
		return $this->render('test-target', array());
    }
    
    public function actionTestRest() 
    {
		$comps = Yii::$app->db->createCommand('SELECT * FROM companies WHERE comp_deleted = 0')->queryAll();
		
		return $this->render('test-rest', array('comps' => $comps));
    }
}

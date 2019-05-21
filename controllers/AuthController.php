<?php

namespace api\controllers;

use Yii;
use app\models\User;
use yii\base\Module;
use yii\rest\Controller;
use yii\web\Response;
use yii\web\HttpException;
use yii\web\UnauthorizedHttpException;

class AuthController extends Controller
{
	/*private $tokenManager;

    public function __construct(string $id, Module $module, TokenManagerInterface $tokenManager, array $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->tokenManager = $tokenManager;
    }

    public function behaviors()
	{
		$behaviors = parent::behaviors();

		$behaviors['authenticator'] = [
			'class' => CompositeAuth::class,
			'authMethods' => [
				SimpleLoginPasswordAuth::class,
			],
		];

		$behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

		return $behaviors;
	}*/

	// сделаем ка мы по простому
	public function actionIndex()
	{
		$login = Yii::$app->request->post('login', '');
		
		if (empty($login)) {
			throw new HttpException(400, 'Login not specified');
		}
		
		$user = User::findByUsername($login);
		if (empty($user)) {
			throw new UnauthorizedHttpException('Invalid login or password');
		}
		
		$password = Yii::$app->request->post('password', '');
		
		if ($user->password != $password) {
			throw new UnauthorizedHttpException('Invalid login or password');
		}
		
		return $user->accessToken;
	}

    protected function verbs()
    {
        return [
            'index' => ['post'],
        ];
    }
}

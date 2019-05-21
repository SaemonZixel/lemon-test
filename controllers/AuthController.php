<?php

namespace api\controllers;

use common\auth\SimpleLoginPasswordAuth;
use Domain\Manager\TokenManagerInterface;
use yii\base\Module;
use yii\filters\auth\CompositeAuth;
use yii\rest\Controller;
use yii\web\Response;

class AuthController extends Controller
{
    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;

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
	}

    /**
     * @SWG\Post(path="/auth",
     *     tags={"Auth"},
     *     summary="Аутентификация по логину паролю (получение токена)",
     *     description="Время жизни токена: 20 минут. Во всех методах данный токен нужно передавать в хэдере: Authorization: Bearer {{token}} (можно без приставки Bearer)",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/AuthRequest"),
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "",
     *         @SWG\Schema(ref = "#/definitions/Token"),
     *     ),
     *     @SWG\Response(
     *         response = 401,
     *         description = "Доступ запрещен. Неверный логин или пароль",
     *     ),
     *     @SWG\Response(
     *         response = 400,
     *         description = "Неверный запрос. Bad Request. Невалидный JSON, и т.д.",
     *     ),
     * )
     */
	public function actionIndex()
	{
		return [
			'token' => $this->tokenManager->generateToken(\Yii::$app->user->getId()),
		];
	}

    protected function verbs()
    {
        return [
            'index' => ['post'],
        ];
    }
}

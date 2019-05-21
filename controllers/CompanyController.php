<?php

namespace api\controllers;

use Yii;
use app\models\Company;
use app\models\CompanyForm;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CompanyController extends Controller
{
	public function behaviors()
    {
        $behaviors = parent::behaviors();

        // только авторизованным пользователям
        $behaviors['authenticator'] = [
            'class' => 'yii\filters\auth\HttpBearerAuth',
        ];

        return $behaviors;
    }

	/* public function actionIndex()
	{
		return $this->render('@app/views/site/test-rest', ['comps' => Company::findAll(['comp_deleted' => 0])]);
	} */

	public function actionView($id)
	{
		try {
            $company = Company::findOne($id);
        } catch (NotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
		
		return $company;
	}
	
	public function actionList()
	{
		try {
            $companies = Company::findAll(['comp_deleted' => 0]);
        } catch (NotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }
		
		return $companies;
	}

	public function actionCreate()
	{
		$form = new CompanyForm();

        $form->load(\Yii::$app->request->post(), '');
        if ($form->validate()) {
            try {
                $company = new Company();
                if ($company->load(Yii::$app->request->post(), '') && $company->save()) {
					\Yii::$app->response->setStatusCode(201, Response::$httpStatuses[201]);
					return $company;
				}
				else {
					throw new HttpException(500, 'Error! Company not created!');
				}
            } catch (\DomainException $e) {
                throw new HttpException(500, $e->getMessage());
            }
        } else 
			return $form;
	
/*		$company = new Company();
		if ($company->load(Yii::$app->request->post()) && $company->save()) {
			return $this->renderAjax('test-rest-json', ['result' => 'ok', 'comp_id' => $company->id]);
		} else {
			return $this->renderAjax('test-rest-json', ['result' => 'ok', 'company' => $company]);
		}*/
	}

	public function actionUpdate($id)
	{
		$company = Company::findOne($id);

		$form = new CompanyForm();
		$form->setAttributes(\Yii::$app->request->post());
		if ($form->validate()) {
			try {
// 				Yii::error(__FILE__.':'.__LINE__.' '.var_export($company->load(Yii::$app->request->post(), ''), true));
				if ($company->load(Yii::$app->request->post(), '') and $company->save()) {
					\Yii::$app->response->setStatusCode(201, Response::$httpStatuses[201]);
					return $company;
				}
				else {
					throw new HttpException(500, 'Error! Company not saved!');
 				}
			} catch (NotFoundException $e) {
				throw new NotFoundHttpException($e->getMessage());
			} catch (\DomainException $e) {
				throw new HttpException(500, $e->getMessage());
			}
		} else 
			return $form;
	
/*		$company = $this->findCompany($id);
		if ($company->load(Yii::$app->request->post()) && $company->save()) {
			return $this->renderAjax('test-rest-json', ['result' => 'ok', 'comp_id' => $company->id]);
		} else {
			return $this->renderAjax('test-rest-json', ['result' => 'ok', 'company' => $company]);
		} */
	}

	public function actionDelete($id)
	{
		try {
			$company = Company::findOne($id);
			$company->comp_deleted = 1;
			if ($company->save()) {
				\Yii::$app->response->setStatusCode(201, Response::$httpStatuses[201]);
				return $company;
			}
			else {
				throw new HttpException(500, 'Error! Company not deleted!');
			}
		} catch (NotFoundException $e) {
			throw new NotFoundHttpException($e->getMessage());
		} catch (\DomainException $e) {
			throw new HttpException(500, $e->getMessage());
		}
	}
}

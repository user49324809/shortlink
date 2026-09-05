<?php

namespace app\controllers;

use app\models\ShortUrl;
use app\models\ShortUrlHit;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use Yii;
use yii\web\Controller;
use yii\web\UnauthorizedHttpException;

class AdminController extends Controller
{
    public function beforeAction($action): bool
    {
        $expectedUser = (string) Yii::$app->params['adminUsername'];
        $expectedPassword = (string) Yii::$app->params['adminPassword'];
        $user = (string) Yii::$app->request->authUser;
        $password = (string) Yii::$app->request->authPassword;

        if (
            $expectedUser === '' ||
            $expectedPassword === '' ||
            !hash_equals($expectedUser, $user) ||
            !hash_equals($expectedPassword, $password)
        ) {
            Yii::$app->response->headers->set('WWW-Authenticate', 'Basic realm="ShortLink Admin"');
            throw new UnauthorizedHttpException('Требуется авторизация администратора.');
        }

        return parent::beforeAction($action);
    }

    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['get'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $linksProvider = new ActiveDataProvider([
            'query' => ShortUrl::find()->orderBy(['id' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => ['id', 'short_code', 'hits_count', 'created_at', 'updated_at'],
            ],
        ]);

        $logsProvider = new ActiveDataProvider([
            'query' => ShortUrlHit::find()
                ->alias('h')
                ->joinWith('shortUrl s')
                ->orderBy(['h.id' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'linksProvider' => $linksProvider,
            'logsProvider' => $logsProvider,
        ]);
    }
}

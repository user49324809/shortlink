<?php

namespace app\controllers;

use app\models\ShortUrl;
use app\models\ShortUrlHit;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;

class AdminController extends Controller
{
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

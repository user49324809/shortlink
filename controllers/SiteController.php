<?php

namespace app\controllers;

use app\components\UrlShortenerService;
use app\models\ShortenForm;
use Throwable;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{
    public function actions(): array
    {
        return [
            'error' => [
                'class' => yii\web\ErrorAction::class,
            ],
        ];
    }

    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'shorten' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $model = new ShortenForm();
        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionShorten(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new ShortenForm();
        $model->load(Yii::$app->request->post(), '');

        if (!$model->validate()) {
            return [
                'success' => false,
                'message' => current($model->getFirstErrors()) ?: 'Ошибка валидации.',
            ];
        }

        /** @var UrlShortenerService $service */
        $service = Yii::createObject(UrlShortenerService::class);

        if (!$service->isUrlAvailable($model->url)) {
            return [
                'success' => false,
                'message' => 'Данный URL не доступен',
            ];
        }

        try {
            $shortUrl = $service->getOrCreateShortUrl($model->url);
            $shortLink = $service->buildShortUrl($shortUrl->short_code);
            $qrCode = $service->buildQrCodeDataUri($shortLink);

            return [
                'success' => true,
                'message' => 'Короткая ссылка успешно создана.',
                'data' => [
                    'originalUrl' => $shortUrl->original_url,
                    'shortCode' => $shortUrl->short_code,
                    'shortLink' => $shortLink,
                    'qrCode' => $qrCode,
                    'hitsCount' => $shortUrl->hits_count,
                ],
            ];
        } catch (Throwable $e) {
            Yii::error($e->getMessage(), __METHOD__);
            return [
                'success' => false,
                'message' => 'Внутренняя ошибка сервиса. Попробуйте позже.',
            ];
        }
    }
}

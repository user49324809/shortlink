<?php

namespace app\controllers;

use app\components\UrlShortenerService;
use app\models\ShortUrl;
use app\models\ShortUrlHit;
use Yii;
use yii\db\Expression;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class RedirectController extends Controller
{
    public function actionGo(string $code)
    {
        $model = ShortUrl::findOne(['short_code' => $code]);

        if ($model === null) {
            throw new NotFoundHttpException('Короткая ссылка не найдена.');
        }

        /** @var UrlShortenerService $service */
        $service = Yii::createObject(UrlShortenerService::class);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $hit = new ShortUrlHit();
            $hit->short_url_id = $model->id;
            $hit->ip_address = $service->detectClientIp();
            $hit->user_agent = (string) Yii::$app->request->userAgent;
            $hit->referer = (string) Yii::$app->request->referrer;
            $hit->created_at = time();
            $hit->save(false);

            ShortUrl::updateAll([
                'hits_count' => new Expression('hits_count + 1'),
                'updated_at' => time(),
            ], ['id' => $model->id]);

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage(), __METHOD__);
        }

        return $this->redirect($model->original_url, 302);
    }
}

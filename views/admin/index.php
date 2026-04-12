<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $linksProvider */
/** @var yii\data\ActiveDataProvider $logsProvider */

use app\models\ShortUrl;
use app\models\ShortUrlHit;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Панель администратора';
?>

<div class="admin-index py-4 py-md-5">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h2 mb-1"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">Список созданных коротких ссылок, счетчики переходов и последние логи.</p>
        </div>
        <div>
            <a class="btn btn-outline-primary" href="<?= Url::to(['site/index']) ?>">На главную</a>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
            <div class="p-3 border-bottom">
                <h2 class="h5 mb-0">Короткие ссылки</h2>
            </div>
            <?= GridView::widget([
                'dataProvider' => $linksProvider,
                'tableOptions' => ['class' => 'table table-striped table-hover align-middle mb-0'],
                'layout' => "{items}\n<div class=\"p-3\">{pager}</div>",
                'columns' => [
                    [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width:70px'],
                    ],
                    [
                        'attribute' => 'original_url',
                        'format' => 'raw',
                        'value' => static function (ShortUrl $model): string {
                            return Html::a(
                                Html::encode($model->original_url),
                                $model->original_url,
                                ['target' => '_blank', 'rel' => 'noopener noreferrer']
                            );
                        },
                    ],
                    [
                        'label' => 'Короткая ссылка',
                        'format' => 'raw',
                        'value' => static function (ShortUrl $model): string {
                            $url = Url::to(['redirect/go', 'code' => $model->short_code], true);
                            return Html::a(Html::encode($url), $url, ['target' => '_blank', 'rel' => 'noopener noreferrer']);
                        },
                    ],
                    [
                        'attribute' => 'hits_count',
                        'headerOptions' => ['style' => 'width:120px'],
                    ],
                    [
                        'attribute' => 'created_at',
                        'label' => 'Создано',
                        'value' => static fn (ShortUrl $model): string => date('d.m.Y H:i:s', (int) $model->created_at),
                        'headerOptions' => ['style' => 'width:170px'],
                    ],
                ],
            ]) ?>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="p-3 border-bottom">
                <h2 class="h5 mb-0">Последние переходы</h2>
            </div>
            <?= GridView::widget([
                'dataProvider' => $logsProvider,
                'tableOptions' => ['class' => 'table table-striped table-hover align-middle mb-0'],
                'layout' => "{items}\n<div class=\"p-3\">{pager}</div>",
                'columns' => [
                    [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width:70px'],
                    ],
                    [
                        'label' => 'Код',
                        'value' => static function (ShortUrlHit $model): string {
                            return $model->shortUrl->short_code ?? '-';
                        },
                        'headerOptions' => ['style' => 'width:100px'],
                    ],
                    [
                        'label' => 'IP',
                        'attribute' => 'ip_address',
                        'headerOptions' => ['style' => 'width:160px'],
                    ],
                    [
                        'label' => 'Referer',
                        'format' => 'ntext',
                        'value' => static fn (ShortUrlHit $model): string => $model->referer ?: '-',
                    ],
                    [
                        'label' => 'User-Agent',
                        'format' => 'ntext',
                        'value' => static fn (ShortUrlHit $model): string => $model->user_agent ?: '-',
                    ],
                    [
                        'attribute' => 'created_at',
                        'label' => 'Дата',
                        'value' => static fn (ShortUrlHit $model): string => date('d.m.Y H:i:s', (int) $model->created_at),
                        'headerOptions' => ['style' => 'width:170px'],
                    ],
                ],
            ]) ?>
        </div>
    </div>
</div>

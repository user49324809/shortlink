<?php

/** @var yii\web\View $this */
/** @var app\models\ShortenForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Сервис коротких ссылок + QR';
$shortenUrl = Url::to(['site/shorten']);

$js = <<<JS
$('#shorten-form').on('beforeSubmit', function (e) {
    e.preventDefault();
    const form = $(this);
    const resultBox = $('#result-box');
    const submitBtn = $('#submit-btn');

    resultBox.html('');
    submitBtn.prop('disabled', true).text('Проверяем...');

    $.ajax({
        url: '{$shortenUrl}',
        type: 'POST',
        dataType: 'json',
        data: form.serialize(),
        success: function (response) {
            if (!response.success) {
                resultBox.html(
                    '<div class="alert alert-danger mb-0">' + $('<div>').text(response.message).html() + '</div>'
                );
                return;
            }

            const data = response.data;
            resultBox.html(
                '<div class="card shadow-sm">' +
                    '<div class="card-body">' +
                        '<div class="row align-items-center g-4">' +
                            '<div class="col-md-4 text-center">' +
                                '<img src="' + data.qrCode + '" alt="QR code" class="img-fluid border rounded p-2 bg-white" />' +
                            '</div>' +
                            '<div class="col-md-8">' +
                                '<div class="alert alert-success">' + $('<div>').text(response.message).html() + '</div>' +
                                '<p class="mb-2"><strong>Исходный URL:</strong><br><a href="' + data.originalUrl + '" target="_blank" rel="noopener noreferrer">' + data.originalUrl + '</a></p>' +
                                '<p class="mb-2"><strong>Короткая ссылка:</strong><br><a href="' + data.shortLink + '" target="_blank" rel="noopener noreferrer">' + data.shortLink + '</a></p>' +
                                '<p class="mb-0 text-muted"><strong>Переходов:</strong> ' + data.hitsCount + '</p>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            );
        },
        error: function () {
            resultBox.html('<div class="alert alert-danger mb-0">Не удалось выполнить запрос.</div>');
        },
        complete: function () {
            submitBtn.prop('disabled', false).text('OK');
        }
    });

    return false;
});
JS;
$this->registerJs($js);
?>

<div class="site-index py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h2 mb-3"><?= Html::encode($this->title) ?></h1>
                    <p class="text-muted mb-4">Вставьте ссылку, нажмите <strong>OK</strong> и получите короткий URL и QR-код без перезагрузки страницы.</p>

                    <?php $form = ActiveForm::begin([
                        'id' => 'shorten-form',
                        'enableClientValidation' => true,
                        'enableAjaxValidation' => false,
                        'action' => ['site/shorten'],
                        'options' => ['class' => 'mb-4'],
                    ]); ?>

                    <div class="row g-2 align-items-start">
                        <div class="col-md-9">
                            <?= $form->field($model, 'url', [
                                'template' => "{input}\n{error}",
                            ])->textInput([
                                'name' => 'url',
                                'placeholder' => 'https://example.com/page',
                                'class' => 'form-control form-control-lg',
                                'autocomplete' => 'off',
                            ]) ?>
                        </div>
                        <div class="col-md-3 d-grid">
                            <?= Html::submitButton('OK', [
                                'class' => 'btn btn-primary btn-lg',
                                'id' => 'submit-btn',
                            ]) ?>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>

                    <div id="result-box"></div>
                </div>
            </div>
        </div>
    </div>
</div>

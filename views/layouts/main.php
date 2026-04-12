<?php

use app\assets\AppAsset;
use yii\bootstrap5\Html;

AppAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="bg-light">
<?php $this->beginBody() ?>
<nav class="navbar navbar-dark bg-dark shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
        <span class="navbar-brand mb-0 h1">ShortLink + QR</span>
        <div class="d-flex gap-2">
            <a class="btn btn-sm btn-outline-light" href="<?= \yii\helpers\Url::to(['site/index']) ?>">Главная</a>
            <a class="btn btn-sm btn-warning" href="<?= \yii\helpers\Url::to(['admin/index']) ?>">Admin</a>
        </div>
    </div>
</nav>

<main class="container">
    <?= $content ?>
</main>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

<?php

use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

$this->title = $name;
?>
<div class="site-error py-5">
    <div class="alert alert-danger">
        <h1 class="h4 mb-3"><?= Html::encode($this->title) ?></h1>
        <p><?= nl2br(Html::encode($message)) ?></p>
    </div>
</div>

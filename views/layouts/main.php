<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $content string */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <?php /* Html::csrfMetaTags() */ ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
    <header><a style="float:right" href="https://github.com/SaemonZixel/lemon-test">https://github.com/SaemonZixel/lemon-test</a><h2><?= Html::encode($this->title) ?></h2></header>
    <?= $content ?>
    <footer></footer>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
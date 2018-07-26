<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
	<style>
		/*
		.my-navbar { background-color: #eeeeee;border-bottom: 1px solid #bfbfbf;color: #000; }
		.my-navbar a { color: #000; }
		
		.navbar-inverse {
			background-image: linear-gradient(#EEEEEE, #EEEEEE 60%, #EEEEEE);
			background-repeat: no-repeat;
			border-bottom: 1px solid #bfbfbf;
			box-shadow: 0 1px 10px rgba(0, 0, 0, 0.1);
			filter: none;
		}
		.navbar-inverse a{ color: #3a3a3a; } */
	</style>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top', //my-navbar 
			//'style' => 'color: #000;background-color: #eeeeee;border-bottom: 1px solid #bfbfbf;'
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            ['label' => 'Home', 'url' => ['/site/index']],
			[
				'label' => 'Lots', 
				
				'items' => [
					['label' => 'Create New Lot','url' => ['/lot/create'], 'active' => ('/lot/create' == $this->context->id)],
					['label' => 'Current Lot','url' => ['/lot/update'], 'active' => ('/lot/update' == $this->context->id)],
					['label' => 'Lots History','url' => ['/lot'], 'active' => ('/lot' == $this->context->id)],
				]
			],
            ['label' => 'Product', 'url' => ['/product']],
            ['label' => 'Orders', 'url' => ['/openorder/order']],
            ['label' => 'Users', 'url' => ['/user']], 
            Yii::$app->user->isGuest ? (
                ['label' => 'Login', 'url' => ['/site/login'], 'linkOptions' => ['style' => 'color: #000;']]
            ) : (
                '<li>'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                    'Logout (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'btn btn-link logout', 'style' => 'color: #000;']
                )
                . Html::endForm()
                . '</li>'
            )
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; Buchoo <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

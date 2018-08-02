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
		.brandclass {  }
		.navbar-toggle { margin-top: 12px; }
		.navbar-inverse .navbar-toggle { border-color: #eeeeee; }
		.navbar-inverse .navbar-toggle .icon-bar { background-color: #000; }
		.navbar-inverse .navbar-toggle:hover, .navbar-inverse .navbar-toggle:focus { background-color: #eeeeee; }
		.navbar-inverse .btn-link { color: #000; margin-top:3px; }
		.navbar-inverse .btn-link:hover, .navbar-inverse .btn-link:focus { font-weight: bold; color: #000 }
		.navbar-inverse .navbar-nav > li > a { color: #000; margin-top:3px;	}
		.navbar-inverse .navbar-nav > li > a:hover, .navbar-inverse .navbar-nav > li > a:focus { font-weight: bold;color: #000; }
		.navbar-inverse .navbar-nav > .open > a, .navbar-inverse .navbar-nav > .open > a:hover, .navbar-inverse .navbar-nav > .open > a:focus {
			background-color: #eeeeee;
			font-weight: bold;
			color: #000;
		}
		.navbar-inverse { 
			background-color: #eeeeee; 
			border-bottom: 2.75px solid #bfbfbf;
			min-height: 60px;
		}
	</style>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        //'brandLabel' => Yii::$app->name,
        'brandLabel' => Html::img('https://cdn.iconscout.com/public/images/icon/free/png-256/google-logo-39642b4be634b363-256x256.png', ['height'=>'50px', 'alt'=>Yii::$app->name, 'title'=>Yii::$app->name, 'style' => 'margin-top:-12px;']),
		'brandOptions' => ['class' => 'brandclass'],//options of the brand
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top', //my-navbar 
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            ['label' => 'Home', 'url' => ['/site/index'], 'active' => ('/site/index' == $this->context->id)],
			[
				'label' => 'Lots', 
				'items' => [
					['label' => 'Create New Lot','url' => ['/lot/create'], 'active' => ('/lot/create' == $this->context->id)],
					['label' => 'Current Lot','url' => ['/lot/update'], 'active' => ('/lot/update' == $this->context->id)],
					['label' => 'Lots History','url' => ['/lot'], 'active' => ('/lot' == $this->context->id)],
					['label' => 'Discount Managment','url' => ['/discount'], 'active' => ('/lot' == $this->context->id)],
				]
			],
			[
				'label' => 'Products', 
				'items' => [
					['label' => 'Add Product by Barcode','url' => ['/product/add-products-by-upc'], 'active' => ('/product/add-products-by-upc' == $this->context->id)],
					['label' => 'Edit Unfinished Product','url' => ['/product/add-products'], 'active' => ('/product/add-products' == $this->context->id)],
					['label' => 'Products List','url' => ['/product'], 'active' => ('/product' == $this->context->id)],
				]
			],
            ['label' => 'Orders', 'url' => ['/openorder/order']],
            ['label' => 'Users', 'url' => ['/user']], 
            Yii::$app->user->isGuest ? (
                ['label' => 'Login', 'url' => ['/site/login'], 'linkOptions' => ['style' => '']]
            ) : (
                '<li>'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                    'Logout (' . Yii::$app->user->identity->name . ')',
                    ['class' => 'btn btn-link logout', 'style' => '']
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

        <!--<p class="pull-right"><?= Yii::powered() ?></p>-->
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

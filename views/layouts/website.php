<?php

/* @var $this \yii\web\View */
/* @var $content string */

//use yii\bootstrap\Nav;
//use yii\bootstrap\NavBar;
//use yii\widgets\Breadcrumbs;
//use app\widgets\Alert;
use app\assets\WebsiteAsset;
use yii\helpers\Url;
use yii\helpers\Html;

WebsiteAsset::register($this);
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
	</head>
	<body>
		<?php $this->beginBody() ?>

			<div class="wrap">
				<?= $this->render('_website_header') ?>
				
				<?= $content ?>
				
				<?= $this->render('_website_footer') ?>
			</div>

		<?php $this->endBody() ?>
	</body>
</html>
<?php $this->endPage() ?>

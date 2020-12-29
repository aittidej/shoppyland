<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Product */

$this->title = 'Update Product: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->product_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="product-update">

    <h1><?= Html::encode($this->title) ?></h1>
	
	<center>
		<?php
			if($model->brand_id == 1)
				echo Html::a('Coach Scan Tools', 'https://scan.coach.com/product/'.$model->upc, [ 'title' => 'Coach System', 'target'=>'_blank' ])."<br>";
				
			$imagePaths = $model->image_path;
			if(empty($imagePaths) || count($imagePaths) == 1)
				echo Html::img($model->firstImage, []);
			else
			{
				foreach($imagePaths AS $imagePath)
				{
					if (strpos($imagePath, 'http') !== false)
						echo Html::img($imagePath, []);
					else
						echo Html::img(Url::base(true) .'/'. $imagePath, []);
				}
			}
		?>
	</center>
	
    <?= $this->render('_form', [
        'model' => $model,
		'upload' => $upload
    ]) ?>

</div>

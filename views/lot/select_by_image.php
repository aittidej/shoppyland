<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

use app\models\DiscountList;

$this->params['breadcrumbs'][] = ['label' => 'Lots', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lot_id, 'url' => ['update', 'id' => $model->lot_id]];
$this->params['breadcrumbs'][] = 'Select By Image';

Pjax::begin();
?>
	<div class='col-sm-12'>
		<center>
		<?php
			echo Html::beginForm(['/lot/select-by-image', 'id'=>$id], 'post', ['data-pjax' => '', 'class' => 'form-inline']);
				echo Html::input('text', 'upc', NULL, ['class' => 'form-control', 'style'=>'width:300px;', 'placeholder'=>'UPC']);
				echo Html::submitButton('Add & Show Related', ['class' => 'btn btn-primary', 'name' => 'hash-button']);
			echo Html::endForm();
		?>
		</center>
	</div>

	<div class='col-sm-12' id='releated-product'>
		<center>
		<?php
			if(!empty($products))
			{
				foreach($products AS $product)
				{
					$img = "<div class='col-sm-2'>".Html::img($product->firstImage, ['style'=>'width: 100%; padding: 3px;cursor: pointer;', 'title'=>$product->title])."<br>".$product->upc."<br>[".$product->model."]</div>";
					echo Html::a($img, 'javascript:void(0);', ['data-product_id'=>$product->product_id, 'id'=>'id-'.$product->product_id, 'class'=>'product-list']);
				}
			}
		?>
		</center>
	</div>

	<?php $this->registerJs("
		var lot_id = '".$model->lot_id."';
		$('#releated-product').on('click','.product-list', function(e){
			var product_id = $(this).data('product_id');
			$('#id-'+product_id).hide(500);
			
			$.ajax({
				url: '" . Yii::$app->getUrlManager()->createUrl('lot/selected-product') . "',
				type: 'POST',
				data: { lot_id:lot_id, product_id:product_id },
				success: function(result) {
					console.log(result);
				},
				error: function(err) {
					console.log(err);
				}
			});
		});
	",View::POS_READY); ?>

<?php Pjax::end(); ?>
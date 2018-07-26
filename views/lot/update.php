<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\DiscountList;

/* @var $this yii\web\View */
/* @var $model app\models\Lot */

$this->title = 'Update Lot #' . $model->lot_number;
$this->params['breadcrumbs'][] = ['label' => 'Lots', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lot_id, 'url' => ['view', 'id' => $model->lot_id]];
$this->params['breadcrumbs'][] = 'Update';

$discountLists = ArrayHelper::map(DiscountList::find()->where(['status'=>'1'])->orderby('title ASC')->all(), 'discount_list_id', 'title');
?>
<div class="lot-update">

    <h1><?= Html::encode($this->title) ?></h1>
	<div class="clearfix"></div><br>
	<div class='col-sm-12 col-md-2 col-lg-2'>
		<?php $form = ActiveForm::begin(); ?>
			
			<?= $form->field($model, 'discount_list_id')->label('Discount')->dropDownList($discountLists, ['prompt'=>'Select discount...', 'required'=>true]); ?>
			<?= $form->field($model, 'items')->label('Barcode (UPC)')->textarea(['rows' => '20', 'id'=>'items-field', 'required'=>'required']) ?>

			<div class="clearfix"></div><br>
			<div class="form-group">
				<center><?= Html::submitButton('Load', ['class' => 'btn btn-success']) ?></center>
			</div>

		<?php ActiveForm::end(); ?>
	</div>
	
	<div class='col-sm-12 col-md-10 col-lg-10'>
		<div class="clearfix"></div><br>
		<?php foreach($lotRels AS $lotRel) { ?>
				<div class='col-sm-12 col-md-2 col-lg-3'>
					<?php  
						$product = $lotRel['product'];
						echo Html::img($product->firstImage, ['width'=>'100%']);
						echo "<br>$product->upc";
						echo "<br># $product->model";
					?>
				</div>
				<div class='col-sm-12 col-md-3 col-lg-3'>
					<?= $form->field($lotRel, 'price')->label('Price ($)')->textInput(['class'=>'form-control price', 'id'=>'price-'.$lotRel->lot_rel_id, 'data-lot_rel_id'=>$lotRel->lot_rel_id]) ?>
				</div>
				<div class='col-sm-12 col-md-3 col-lg-3'>
					<?= $form->field($lotRel, 'discount_list_id')->label('Discount')->dropDownList($discountLists, ['class'=>'form-control discount', 'id'=>'discount-'.$lotRel->lot_rel_id, 'prompt'=>'Select discount...', 'data-lot_rel_id'=>$lotRel->lot_rel_id]); ?>
				</div>
				<div class='col-sm-12 col-md-3 col-lg-3'>
					<?php  
						$lotRel->subtotal = Yii::$app->controller->priceDiscountCalculator($lotRel->price, $lotRel->discount_list_id);
						echo $form->field($lotRel, 'subtotal')->label('Total ($)')->textInput(['class'=>'form-control subtotal', 'id'=>'subtotal-'.$lotRel->lot_rel_id, 'data-lot_rel_id'=>$lotRel->lot_rel_id, 'readonly'=>'readonly']);
					?>
				</div>
			<div class="clearfix"></div><br>
		<?php } ?>
	</div>
</div>

<?php $this->registerJs("

	$('.price').focusout(function (e) {
		var lot_rel_id = $(this).data('lot_rel_id');
		var price = $(this).val();
		var discount_id = $('#discount-'+lot_rel_id).val();
		
		calculate(lot_rel_id, price, discount_id);
	});
	
	$('.discount').change(function (e) {
		var lot_rel_id = $(this).data('lot_rel_id');
		var price = $('#price-'+lot_rel_id).val();
		var discount_id = $(this).val();
		
		calculate(lot_rel_id, price, discount_id);
	});
	
	function calculate(lot_rel_id, price, discount_id)
	{
		$.ajax({
			url: '" . Yii::$app->getUrlManager()->createUrl('lot/calculate-price') . "',
			type: 'POST',
			data: { price:price, discount_id:discount_id, lot_rel_id:lot_rel_id },
			success: function(result) {
				console.log(result);
				
				$('#subtotal-'+lot_rel_id).val(result);
			},
			error: function(err) {
				console.log(err);
			}
		});
	}

",View::POS_READY); ?>

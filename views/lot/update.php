<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\DiscountList;

/* @var $this yii\web\View */
/* @var $model app\models\Lot */

$this->title = 'Update Lot #' . $model->lot_number . ' ('.(empty($model->user_id) ? 'All buyers' : $model->user->name).')';
$this->params['breadcrumbs'][] = ['label' => 'Lots', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lot_id, 'url' => ['view', 'id' => $model->lot_id]];
$this->params['breadcrumbs'][] = 'Update';

$discountListModel = DiscountList::find()->where(['status'=>'1'])->orderby('title ASC')->all();
$discountLists = ArrayHelper::map($discountListModel, 'discount_list_id', 'title');
?>
<style>
hr {  border: 0; height: 1px; background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0)); }
</style>
<div class="lot-update">

	<div class='col-sm-10'>
		<h1><?= Html::encode($this->title) ?></h1>
	</div>
	<div class='col-sm-2'>
		<br><br>
		<?= Html::a('Add by UPC', ['/lot/select-by-image', 'id'=>$model->lot_id], ['class' => 'btn btn-success']) ?>
		<?= Html::a('Add by Image', ['/lot/select-by-image', 'id'=>$model->lot_id], ['class' => 'btn btn-success']) ?>
	</div>
	
	<div class="clearfix"></div><br>
	<!--<div class='col-sm-12 col-md-2 col-lg-2'>
		<?php $form = ActiveForm::begin(); ?>
			<strong>Price: $<span id="calculated-result"></span></strong>
			<?= $form->field($model, 'discount_list_id')->label('Discount')->dropDownList($discountLists, ['prompt'=>'Select discount...', 'id'=>'discount', 'required'=>true]); ?>
			<?= $form->field($model, 'price')->label('Price ($)')->textInput(['id'=>'price']) ?>
			<?= $form->field($model, 'overwrite_total')->label('Overwrite Total ($)')->textInput() ?>
			<?= $form->field($model, 'items')->label('Barcode (UPC)')->textarea(['rows' => '20', 'id'=>'items-field', 'required'=>'required']) ?>

			<div class="clearfix"></div><br>
			<div class="form-group">
				<center><?= Html::submitButton('Load', ['class' => 'btn btn-success']) ?></center>
			</div>

		<?php ActiveForm::end(); ?>
	</div>
	
	<div class='col-sm-12 col-md-10 col-lg-10'>-->
	<div class='col-sm-12 col-md-12 col-lg-12'>
		<div class="clearfix"></div><br>
		<?php foreach($lotRels AS $lotRel) { ?>
			<div id='lot-rel-row-<?= $lotRel->lot_rel_id ?>'>
				<div class='col-sm-12 col-md-2 col-lg-3'>
					<?php  
						$product = $lotRel['product'];
						echo "<a href='".Yii::$app->getUrlManager()->createUrl(['product/update', 'id'=>$product->product_id])."' target='_blank'>";
							echo Html::img($product->firstImage, ['width'=>'100%']);
							echo "<br>$product->upc";
							echo "<br>".Html::a(" <i class='glyphicon glyphicon-copy'></i>", 'javascript:void(0);', [
														'data-lot_rel_id' => $lotRel->lot_rel_id,
														'data-product_id' => $product->product_id,
														'class' => 'duplicate',
														'title' => 'Duplicate',
														'style'=>'color: green;',
													]);
							if(empty($lotRel->bought_date))
								echo " # ".$product->model;
							else
								echo " # $product->model (".date('m/d/Y', strtotime($lotRel->bought_date)).")";
						echo "</a>";
						echo Html::a(" <i class='glyphicon glyphicon-trash'></i>", 'javascript:void(0);', [
													'data-lot_rel_id' => $lotRel->lot_rel_id,
													'class' => 'delete',
													'title' => 'Delete',
													'style'=>'color: red;',
												]);
					?>
				</div>
				
				
				
				
				
				<div class='col-sm-12 col-md-3 col-lg-2'>
					<?= $form->field($lotRel, 'price')->label('Price ($)')->textInput(['class'=>'form-control price', 'id'=>'price-'.$lotRel->lot_rel_id, 'data-lot_rel_id'=>$lotRel->lot_rel_id]) ?>
				</div>
				<div class='col-sm-12 col-md-3 col-lg-3'>
					<?= $form->field($lotRel, 'discount_list_id')->label('Discount')->dropDownList($discountLists, ['class'=>'form-control discount', 'id'=>'discount-'.$lotRel->lot_rel_id, 'prompt'=>'Select discount...', 'data-lot_rel_id'=>$lotRel->lot_rel_id]); ?>
				</div>
				<div class='col-sm-12 col-md-3 col-lg-2'>
					<?php  
						if(empty($lotRel->overwrite_total))
							$lotRel->total = Yii::$app->controller->priceDiscountCalculator($lotRel->price, $lotRel->discount_list_id);
						else
							$lotRel->total = $lotRel->overwrite_total;
						echo $form->field($lotRel, 'total')->label('Total ($)')->textInput(['class'=>'form-control total', 'id'=>'total-'.$lotRel->lot_rel_id, 'data-lot_rel_id'=>$lotRel->lot_rel_id, 'readonly'=>'readonly']);
					?>
				</div>
				<div class='col-sm-12 col-md-3 col-lg-2'>
					<?= $form->field($lotRel, 'overwrite_total')->label('Overwrite Total')->textInput(['class'=>'form-control overwrite', 'id'=>'overwrite-'.$lotRel->lot_rel_id, 'data-lot_rel_id'=>$lotRel->lot_rel_id]) ?>
				</div>
				
				
				
				
				<div class="clearfix"></div><br><hr><br>
			</div>
		<?php } ?>
	</div>
</div>

<?php $this->registerJs("

	$('.duplicate').click(function (e) {
		var lotRelId = $(this).data('lot_rel_id');
		alert(lotRelId);
		
		var priceText = \"\";
	});
	
	$('.delete').click(function (e) {
		var lotRelId = $(this).data('lot_rel_id');
		$.ajax({
			url: '".Yii::$app->getUrlManager()->createUrl('lot/lot-rel-delete')."',
			type: 'POST',
			data: { lotRelId: lotRelId  },
			beforeSend: function() {
				$('#lot-rel-row-'+lotRelId).css('background-color', '#d68380')
			},
			success: function(result) {
				$('#lot-rel-row-'+lotRelId).remove();
			},
			error: function(err) {
				console.log(err);
			}
		});
	});

	$('#price').focusout(function (e) {
		var price = $(this).val();
		var discount_id = $('#discount').val();
		
		preCalculate(price, discount_id)
	});
	
	$('#discount').change(function (e) {
		var price = $('#price').val();
		var discount_id = $(this).val();
		
		preCalculate(price, discount_id)
	});
	
	$('.price').focusout(function (e) {
		var lot_rel_id = $(this).data('lot_rel_id');
		var price = $(this).val();
		var discount_id = $('#discount-'+lot_rel_id).val();
		
		calculate(lot_rel_id, price, discount_id);
	});
	
	$('.overwrite').focusout(function (e) {
		var lot_rel_id = $(this).data('lot_rel_id');
		var overwrite = $(this).val();
		var price = $('#price-'+lot_rel_id).val();
		var discount_id = $('#discount-'+lot_rel_id).val();
		
		calculate(lot_rel_id, price, discount_id, overwrite);
	});
	
	$('.discount').change(function (e) {
		var lot_rel_id = $(this).data('lot_rel_id');
		var price = $('#price-'+lot_rel_id).val();
		var discount_id = $(this).val();
		
		calculate(lot_rel_id, price, discount_id);
	});
	
	function preCalculate(price, discount_id)
	{
		$.ajax({
			url: '" . Yii::$app->getUrlManager()->createUrl('lot/pre-calculate-price') . "',
			type: 'POST',
			data: { price:price, discount_id:discount_id },
			success: function(result) {
				console.log(result);
				
				$('#calculated-result').html(result);
				
			},
			error: function(err) {
				console.log(err);
			}
		});
	}
	
	function calculate(lot_rel_id, price, discount_id, overwrite = 0)
	{
		$.ajax({
			url: '" . Yii::$app->getUrlManager()->createUrl('lot/calculate-price') . "',
			type: 'POST',
			data: { price:price, discount_id:discount_id, lot_rel_id:lot_rel_id, overwrite:overwrite },
			success: function(result) {
				console.log(result);
				
				if(result != overwrite)
					$('#overwrite-'+lot_rel_id).val('');
				
				$('#total-'+lot_rel_id).val(result);
				
			},
			error: function(err) {
				console.log(err);
			}
		});
	}

",View::POS_READY); ?>

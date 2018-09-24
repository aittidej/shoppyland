<?php

use yii\web\View;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $openOrder app\models\OpenOrder */

$user = $openOrder->user;
$lot = $openOrder->lot;
$this->title = "Pricing ".$user->name."'s Order - Lot #".$lot->lot_number;
$this->params['breadcrumbs'][] = ['label' => 'Open Orders', 'url' => ['/openorder/order/index']];
$this->params['breadcrumbs'][] = $this->title;
$total = $totalQty = 0;
?>
<style type="text/css">
.tftable {font-size:12px;color:#333333;width:100%;border-width: 0px;border-color: #729ea5;border-collapse: collapse;}
.tftable th {font-size:12px;background-color:#acc8cc;border-width: 0px;padding: 8px;border-style: solid;border-color: #729ea5;text-align:left;}
.tftable tr {background-color:#ffffff;}
.tftable td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #000;}
</style>

<div class="open-order-view">
	<div class='col-sm-7'>
		<h1><?= Html::encode($this->title) ?></h1>
	</div>
	<div class='col-sm-5'><br>
		<?= Html::a('<i class="glyphicon glyphicon-plus"></i> Add More Items', ['/openorder/order/add-items', 'id'=>$openOrder->open_order_id], ['class' => 'btn btn-success']) ?> 
		<?= Html::a('<i class="glyphicon glyphicon-pencil"></i> Update Order', ['/openorder/order/update', 'id'=>$openOrder->open_order_id], ['class' => 'btn btn-warning']) ?> 
		<?= Html::a('View Report', ['/openorder/report', 'id'=>$openOrder->open_order_id], ['class' => 'btn btn-info']) ?>
	</div>
	
	<?php $form = ActiveForm::begin(); ?>
		
		<div class='col-sm-12'>
			<table class="tftable" border="0">
			<?php foreach($openOrderRels AS $index => $openOrderRel) { ?>
				<?php 
					$product = $openOrderRel['product']; 
					$lotRels = $lot->getLotRelByProduct($product->product_id);
				?>
					<tr class='product-row-<?= $product->product_id ?>'>
						<td width="25%" rowspan="2">
							<?php 
								//echo Html::img($product->firstImage, ['width'=>'100%']);
								echo Html::a('Delete', 'javascript:void(0);', ['class'=>'btn btn-danger delete', 'data-product_id'=>$product->product_id, 'data-open_order_rel_id'=>$openOrderRel->open_order_rel_id, 'style'=>'width: 100%;']);
								 
							?>
						</td>
						<td colspan="3">
							<?php 
								echo "<h4><strong>".Html::a($product->title, ['/product/update', 'id'=>$product->product_id], ['target'=>'_blank'])."</strong><br>Model #".$product->model."<br>UPC: ".$product->upc."</h4>"; 
								if($openOrderRel->qty > 1)
									echo Html::a('Split', 'javascript:void(0);', ['class'=>'btn btn-info split', 'data-product_id'=>$product->product_id, 'data-open_order_rel_id'=>$openOrderRel->open_order_rel_id, 'style'=>'width: 100%;']);
								
								if(!empty($lotRels))
								{
									echo "<h6>There are ".count($lotRels)." prices:</h6>";
									foreach($lotRels AS $lotRel)
									{
										echo "$".$lotRel->unitPrice."<br>";
									}
								}
							?>
						</td>
					</tr>
					<tr>
						<td width="25%">
							<?php
								echo $form->field($openOrderRel, "[$index]qty")->textInput(['type' => 'number', 'id'=>'primary-qty-'.$openOrderRel->open_order_rel_id, 'class'=>'form-control primary', 'data-open_order_rel_id'=>$openOrderRel->open_order_rel_id]);
							?>
							<span id='qty-<?= $openOrderRel->open_order_rel_id ?>'></span>
						</td>
						<td width="25%">
							<?php 
								if(empty($openOrderRel->unit_price))
									$openOrderRel->unit_price = NULL;
								echo $form->field($openOrderRel, "[$index]unit_price")->label('Price ($)'.($openOrderRel->need_attention ? "<span style='color: red;'> - Need Attention</span>" : ''))->textInput(['type' => 'number', 'class'=>'form-control primary', 'id'=>'primary-price-'.$openOrderRel->open_order_rel_id, 'data-open_order_rel_id'=>$openOrderRel->open_order_rel_id]);
							?>
							<span id='price-<?= $openOrderRel->open_order_rel_id ?>'></span>
						</td>
						<td>
							<?php
								$openOrderRel->subtotal = $openOrderRel->qty*$openOrderRel->unit_price;
								$totalQty += $openOrderRel->qty;
								$total += $openOrderRel->subtotal;
								echo $form->field($openOrderRel, "[$index]subtotal")->label('Subtotal ($)')->textInput(['maxlength' => true, 'disabled'=>'disabled', 'id'=>'primary-subtotal-'.$openOrderRel->open_order_rel_id, 'data-product_id'=>$product->product_id, 'data-open_order_rel_id'=>$openOrderRel->open_order_rel_id]);
							?>
							<span id='subtotal-<?= $openOrderRel->open_order_rel_id ?>'></span>
						</td>
					</tr>
			<?php } ?>
				<tr>
					<th><strong>Total</strong></th>
					<th><strong><?= $totalQty ?></strong></th>
					<th></th>
					<th><strong><?= "$".$total ?></strong></th>
				</tr>
			</table>
		</div>
		
		<div class='col-sm-12'><br></div>
		
		<div class="form-group col-sm-12">
			<center><?php // Html::submitButton('Save', ['class' => 'btn btn-success']) ?></center>
		</div>
	
	<?php ActiveForm::end(); ?>

</div>

<?php 
$this->registerJs("

$('.split').click(function (e) {
	var openOrderRelId = $(this).data('open_order_rel_id');
	var errorText = '<p class=\"help-block help-block-error\"></p>';
	
	$.ajax({
		url: '".Yii::$app->getUrlManager()->createUrl('openorder/order/split-price')."',
		type: 'POST',
		data: { openOrderRelId: openOrderRelId },
		success: function(result) {
			if(result)
			{
				//var result = JSON.parse(result);
				console.log(result);
				
				var qty = \"<input type='number' id='new-qty-\"+openOrderRelId+\"' class='form-control' name='qty[]' value='3'>\";
				var price = \"<input type='number' id='new-price-\"+openOrderRelId+\"' class='form-control' name='qty[]' value='3'>\";
				var subtotal = \"<input type='number' id='new-subtotal-\"+openOrderRelId+\"' class='form-control' name='qty[]' value='3'>\";
	
				$('#qty-'+openOrderRelId).append(qty+errorText);
				$('#price-'+openOrderRelId).append(price+errorText);
				$('#subtotal-'+openOrderRelId).append(subtotal+errorText);
			}
			
		},
		error: function(err) {
			console.log(err);
			$('#load-'+orderId).html(\"<i class='glyphicon glyphicon-remove' style='color: red;'></i>\");
		}
	});
	
	
});

$('.primary').focusout(function (e) {
	var openOrderRelId = $(this).data('open_order_rel_id');
	var qty = $('#primary-qty-'+openOrderRelId).val();
	var price = $('#primary-price-'+openOrderRelId).val();
	$('#primary-subtotal-'+openOrderRelId).val(qty*price);
	
	$.ajax({
		url: '".Yii::$app->getUrlManager()->createUrl('openorder/order/change-primary-subtotal')."',
		type: 'POST',
		data: { qty: qty, price: price, openOrderRelId: openOrderRelId  },
		success: function(result) {
			console.log(result);
		},
		error: function(err) {
			console.log(err);
		}
	});
	
	
	/*var keyCode = e.keyCode || e.which;
	if (keyCode == 9 || keyCode == 13) { 
		e.preventDefault(); 
		var vidID = parseInt(this.getAttribute('id'));
		$('#'+(vidID+1)).focus();
	}*/
});


", View::POS_READY);
?>

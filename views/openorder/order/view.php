<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $openOrder app\models\OpenOrder */

$user = $openOrder->user;
$this->title = "Pricing ".$user->name."'s Order - Lot #".$lot->lot_number;
$this->params['breadcrumbs'][] = ['label' => 'Open Orders', 'url' => ['/openorder/order/index']];
$this->params['breadcrumbs'][] = $this->title;
$total = $totalQty = 0;
$currencySymbol = $user->currency_base == "USD" ? "$" : "&#3647;";
?>
<style type="text/css">
.tftable {font-size:12px;color:#333333;width:100%;border-width: 0px;border-color: #729ea5;border-collapse: collapse;}
.tftable th {font-size:12px;background-color:#acc8cc;border-width: 0px;padding: 8px;border-style: solid;border-color: #729ea5;text-align:left;}
.tftable tr {background-color:#ffffff;}
.tftable td {font-size:12px;border-width: 1px;padding: 8px;border-style: solid;border-color: #000;}
</style>

<div class="open-order-view">
	<div class='col-sm-6'>
		<h1><?= Html::encode($this->title) ?></h1>
	</div>
	<div class='col-sm-6'><br>
		<?= Html::a('<i class="glyphicon glyphicon-plus"></i> Add More Items', ['/openorder/order/add-items', 'id'=>$openOrder->open_order_id], ['class' => 'btn btn-success']) ?> 
		<?= Html::a('<i class="glyphicon glyphicon-book"></i> Lots', ['/lot/update', 'id'=>$openOrder->lot_id], ['class' => 'btn btn-default', 'target' => '_blank']) ?> 
		<?= Html::a('<i class="glyphicon glyphicon-pencil"></i> Update Order', ['/openorder/order/update', 'id'=>$openOrder->open_order_id], ['class' => 'btn btn-warning']) ?> 
		<?= Html::a('View Invoice', ['/openorder/report', 'id'=>$openOrder->open_order_id], ['class' => 'btn btn-info']) ?>
	</div>
	
	<?php $form = ActiveForm::begin(); ?>
		
		<div class='col-sm-12'>
			<table class="tftable" border="0">
				<?php foreach($openOrderRels AS $productId => $openOrderRelArrays) { ?>
					<?php 
						$bgColorWarning = empty($openOrderRelArrays[0]['openOrderRel']['unit_price']) ? 'bgcolor="#FFEFA4"' : '';
						$openOrderRelArray = $openOrderRelArrays[0];
						$image = Url::base(true) . "/images/default_image.jpg";
						$product = $openOrderRelArray['product']; 
						$openOrderRel = $openOrderRelArray['openOrderRel']; 
						$OpenOrderRelModel = $openOrderRelArray['OpenOrderRelModel'];
						$openOrderRelId = $openOrderRel['open_order_rel_id']; // first open_order_rel_id
						//$lotRels = $lot->getLotRelByProduct($productId);
						$lotRels = Yii::$app->controller->getLotRelByProduct($allLotRels, $productId);
					?>
					<tr class='product-row-<?= $productId ?>'>
						<td width="25%" rowspan="2">
							<?php 
								$imagePath = json_decode($product['image_path'], true);
								if(!empty($imagePath[0]))
								{
									if (strpos($imagePath[0], 'http') !== false)
										$image = $imagePath[0];
									else
										$image = Url::base(true) .'/'. $imagePath[0];
								}
								echo Html::img($image, ['width'=>'100%']);
								echo Html::a('Delete', 'javascript:void(0);', ['class'=>'btn btn-danger delete', 'data-product_id'=>$productId, 'style'=>'width: 100%;']);
							?>
						</td>
						<td colspan="3" <?= $bgColorWarning ?>>
							<?php 
								echo "<h4><strong>".$product['title']."</strong><br>Model #".$product['model']."<br>UPC: ".Html::a(empty($product['upc']) ? '(no UPC)' : $product['upc'], ['/product/update', 'id'=>$productId], ['target'=>'_blank'])."</h4>"; 
								echo Html::a('Split', 'javascript:void(0);', ['class'=>'btn btn-info split', 'data-product_id'=>$productId, 'data-open_order_rel_id'=>$openOrderRelId, 'style'=>'width: 100%;']);
								
								if(!empty($lotRels) && !empty($openOrderRel['currency']))
								{
									//echo "<h6>There are ".count($lotRels)." price(s):</h6>";
									foreach($lotRels AS $lotRel)
									{
										if($lotRel->currency != $openOrderRel['currency'])
											continue;
										
										$unitPrice = $lotRel->unitPrice;
										if(!empty($unitPrice))
											echo "<h5>$".$unitPrice."</h5>";
									}
								}
							?>
						</td>
					</tr>
					<tr class='product-row-<?= $productId ?>'>
						<td width="25%" <?= $bgColorWarning ?>>
							<?php 
								foreach($openOrderRelArrays AS $index => $eachOpenOrderRel) 
								{
									$showLabel = count($openOrderRelArrays) == 1 || (count($openOrderRelArrays) > 1 && $index == 0);
									$openOrderRel = $eachOpenOrderRel['openOrderRel']; 
									$OpenOrderRelModel->qty = $openOrderRel['qty'];
									$OpenOrderRelModel->reference = $openOrderRel['reference'];
									echo $form->field($OpenOrderRelModel, "qty")
											->label($showLabel ? 'Qty' : false)
											->textInput([
												'id'=>'primary-qty-'.$openOrderRel['open_order_rel_id'], 
												'class'=>'form-control primary', 
												'data-product_id'=>$productId, 
												'data-open_order_rel_id'=>$openOrderRel['open_order_rel_id']
											]);
									echo $form->field($OpenOrderRelModel, "reference")
											->label(false)
											->textInput([
												'id'=>'primary-reference-'.$openOrderRel['open_order_rel_id'], 
												'class'=>'form-control primary', 
												'placeholder'=>'Reference', 
												'data-product_id'=>$productId, 
												'data-open_order_rel_id'=>$openOrderRel['open_order_rel_id']
											]);
								}
								echo "<span id='qty-".$productId."'></span>";
							?>
						</td>
						<td width="25%" <?= $bgColorWarning ?>>
							<?php 
								$applyAll = '';
								if(count($openOrderRelArrays) == 1 && !empty($openOrderRelArrays[0]['openOrderRel']['unit_price']))
									$applyAll = "<span class='apply-all-".$openOrderRelArrays[0]['openOrderRel']['open_order_rel_id']."'>".Html::a(' {Apply to All}', 'javascript:void(0);', ['class'=>'apply-all', 'data-open_order_rel_id'=>$openOrderRelArrays[0]['openOrderRel']['open_order_rel_id']])."</span>";
								foreach($openOrderRelArrays AS $index => $eachOpenOrderRel) 
								{
									$showLabel = count($openOrderRelArrays) == 1 || (count($openOrderRelArrays) > 1 && $index == 0);
									$openOrderRel = $eachOpenOrderRel['openOrderRel']; 
									$OpenOrderRelModel->unit_price = empty($openOrderRel['unit_price']) ? NULL : $openOrderRel['unit_price'];
									echo $form->field($OpenOrderRelModel, "unit_price")
											->label($showLabel ? ("Price ($currencySymbol)".($openOrderRel['need_attention'] ? "<span style='color: red;'> - Need Attention</span>" : '')).$applyAll : false)
											->textInput([
												'class'=>'form-control primary', 
												'data-product_id'=>$productId, 
												'id'=>'primary-price-'.$openOrderRel['open_order_rel_id'], 
												'data-open_order_rel_id'=>$openOrderRel['open_order_rel_id']
											]);
									echo "<br><br><br>";
								}
								echo "<span id='price-".$productId."'></span>";
								
									
							?>
						</td>
						<td <?= $bgColorWarning ?>>
							<?php 
								foreach($openOrderRelArrays AS $index => $eachOpenOrderRel) 
								{
									$showLabel = count($openOrderRelArrays) == 1 || (count($openOrderRelArrays) > 1 && $index == 0);
									$openOrderRel = $eachOpenOrderRel['openOrderRel']; 
									$OpenOrderRelModel->subtotal = $openOrderRel['qty']*$openOrderRel['unit_price'];
									$totalQty += $openOrderRel['qty'];
									$total += $OpenOrderRelModel->subtotal;
									echo $form->field($OpenOrderRelModel, "subtotal")
											->label($showLabel ? "Subtotal ($currencySymbol)&nbsp;&nbsp;[Manually Set: ".($openOrderRel['manually_set'] ? "<i class='glyphicon glyphicon-ok' style='color:green'></i>" : "<i class='glyphicon glyphicon-remove' style='color:red;'></i>")."]" : false)
											->textInput([
												'maxlength' => true, 
												'disabled'=>'disabled', 
												'id'=>'primary-subtotal-'.$openOrderRel['open_order_rel_id'], 
												'data-product_id'=>$productId, 
												'data-open_order_rel_id'=>$openOrderRel['open_order_rel_id']
											]);
									echo "<br><br><br>";
								}
								echo "<span id='subtotal-".$productId."'></span>";
							?>
						</td>
					</tr>
				<?php } ?>
				<tr>
					<th><strong>Total</strong></th>
					<th><strong><?= $totalQty ?></strong></th>
					<th></th>
					<th><strong><?= $currencySymbol.$total ?></strong></th>
				</tr>
			</table>
		</div>
		
		<div class='col-sm-12'><br></div>
		
		<div class="form-group col-sm-12">
			<!--<center><?php // Html::submitButton('Save', ['class' => 'btn btn-success']) ?></center>
			<center><?php //Html::a('Save', ['/openorder/order/view', 'id'=>$openOrder->open_order_id], ['class' => 'btn btn-success']) ?></center>-->
		</div>
	
	<?php ActiveForm::end(); ?>

</div>

<?php 
$this->registerJs("
var openOrderId = ".$openOrder->open_order_id.";

$('.delete').click(function (e) {
	if (confirm('Are you sure?') == true) 
	{
		var productId = $(this).data('product_id');
		$.ajax({
			url: '".Yii::$app->getUrlManager()->createUrl('openorder/order/delete-single')."',
			type: 'POST',
			data: { productId: productId, openOrderId: openOrderId  },
			success: function(result) {
				console.log(result);
				$('.product-row-'+productId).remove();
			},
			error: function(err) {
				console.log(err);
			}
		});
	}
});

$('.apply-all').click(function (e) {
	if (confirm('Are you sure?') == true) 
	{
		var open_order_rel_id = $(this).data('open_order_rel_id');
		$.ajax({
			url: '".Yii::$app->getUrlManager()->createUrl('openorder/order/apply-all')."',
			type: 'POST',
			data: { open_order_rel_id: open_order_rel_id },
			beforeSend: function() {
				$('.apply-all-'+open_order_rel_id).html(\" <img src='https://loading.io/spinners/hourglass/lg.sandglass-time-loading-gif.gif' width='25px' height='25px'>\");
			},
			success: function(result) {
				$('.apply-all-'+open_order_rel_id).html(\" <span class='glyphicon glyphicon-ok' style='color:green;font-size: 16px;'></span>\");
			},
			error: function(err) {
				console.log(err);
			}
		});
	}
});

$('.split').click(function (e) {
	var openOrderRelId = $(this).data('open_order_rel_id');
	var productId = $(this).data('product_id');
	var errorText = '<p class=\"help-block help-block-error\"></p>';
	
	$.ajax({
		url: '".Yii::$app->getUrlManager()->createUrl('openorder/order/split-price')."',
		type: 'POST',
		data: { openOrderId: openOrderId, productId: productId },
		success: function(result) {
			if(result)
			{
				var result = JSON.parse(result);
				console.log(result);
				
				var newOpenOrderRelId = result['newOpenOrderRelId'];
				var newUnitPrice = result['newUnitPrice'];
				var newQty = result['newQty'];
				var newSubtotal = result['newSubtotal'];
				
				var oldQty = result['oldQty'];
				var oldSubtotal = result['oldSubtotal'];
				
				$('#primary-qty-'+openOrderRelId).val(oldQty);
				$('#primary-subtotal-'+openOrderRelId).val(oldSubtotal);
				
				var qtyText = \"<input id='primary-qty-\"+newOpenOrderRelId+\"' class='form-control primary' name='OpenOrderRel[qty]' value='\"+newQty+\"' data-product_id='\"+productId+\"' data-open_order_rel_id='\"+newOpenOrderRelId+\"' type='text'>\";
				var priceText = \"<input id='primary-price-\"+newOpenOrderRelId+\"' class='form-control primary' name='OpenOrderRel[price]' value='\"+newUnitPrice+\"' data-product_id='\"+productId+\"' data-open_order_rel_id='\"+newOpenOrderRelId+\"' type='text'>\";
				var subtotalText = \"<input id='primary-subtotal-\"+newOpenOrderRelId+\"' class='form-control primary' name='OpenOrderRel[subtotal]' value='\"+newSubtotal+\"' data-product_id='\"+productId+\"' data-open_order_rel_id='\"+newOpenOrderRelId+\"' type='text'>\";
	
				$('#qty-'+productId).append(qtyText+errorText);
				$('#price-'+productId).append(priceText+errorText);
				$('#subtotal-'+productId).append(subtotalText+errorText);
			}
			
		},
		error: function(err) {
			console.log(err);
		}
	});
});

$('.tftable').on('focusout', '.primary', function(e){
	var openOrderRelId = $(this).data('open_order_rel_id');
	var qty = $('#primary-qty-'+openOrderRelId).val();
	var reference = $('#primary-reference-'+openOrderRelId).val();
	var price = $('#primary-price-'+openOrderRelId).val();
	$('#primary-subtotal-'+openOrderRelId).val(qty*price);
	
	$.ajax({
		url: '".Yii::$app->getUrlManager()->createUrl('openorder/order/change-primary-subtotal')."',
		type: 'POST',
		data: { qty: qty, reference: reference, price: price, openOrderRelId: openOrderRelId  },
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

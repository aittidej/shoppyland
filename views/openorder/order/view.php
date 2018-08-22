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
		<?= Html::a('View Report', ['/openorder/report', 'id'=>$openOrder->open_order_id], ['class' => 'btn btn-info']) ?>
	</div>
	
	<?php $form = ActiveForm::begin(); ?>
		
		<div class='col-sm-12'>
			<table class="tftable" border="0">
				<?php foreach($openOrderRels AS $productId => $openOrderRelArrays) { ?>
					<?php 
						$lotRels = $lot->getLotRelByProduct($productId);
						$openOrderRelArray = $openOrderRelArrays[0];
						$image = "http://www.topprintltd.com/global/images/PublicShop/ProductSearch/prodgr_default_300.png";
						
						$product = $openOrderRelArray['product']; 
						$openOrderRel = $openOrderRelArray['openOrderRel']; 
						$OpenOrderRelModel = $openOrderRelArray['OpenOrderRelModel'];
						
						$openOrderRelId = $openOrderRel['open_order_rel_id']; // first open_order_rel_id
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
						<td colspan="3">
							<?php 
								echo "<h4><strong>".Html::a($product['title'], ['/product/update', 'id'=>$productId], ['target'=>'_blank'])."</strong><br>Model #".$product['model']."<br>UPC: ".$product['upc']."</h4>"; 
								echo Html::a('Split', 'javascript:void(0);', ['class'=>'btn btn-info split', 'data-product_id'=>$productId, 'data-open_order_rel_id'=>$openOrderRelId, 'style'=>'width: 100%;']);
								
								if(!empty($lotRels))
								{
									echo "<h6>There are ".count($lotRels)." prices:</h6>";
									foreach($lotRels AS $lotRel)
									{
										$unitPrice = $lotRel->unitPrice;
										if(!empty($unitPrice))
											echo "$".$unitPrice."<br>";
									}
								}
							?>
						</td>
					</tr>
					<tr class='product-row-<?= $productId ?>'>
						<td width="25%">
							<?php 
								foreach($openOrderRelArrays AS $index => $eachOpenOrderRel) 
								{
									$showLabel = count($openOrderRelArrays) == 1 || (count($openOrderRelArrays) > 1 && $index == 0);
									$openOrderRel = $eachOpenOrderRel['openOrderRel']; 
									$OpenOrderRelModel->qty = $openOrderRel['qty'];
									echo $form->field($OpenOrderRelModel, "qty")
											->label($showLabel ? 'Qty' : false)
											->textInput([
												'id'=>'primary-qty-'.$openOrderRel['open_order_rel_id'], 
												'class'=>'form-control primary', 
												'data-product_id'=>$productId, 
												'data-open_order_rel_id'=>$openOrderRel['open_order_rel_id']
											]);
								}
								echo "<span id='qty-".$productId."'></span>";
							?>
						</td>
						<td width="25%">
							<?php 
								foreach($openOrderRelArrays AS $index => $eachOpenOrderRel) 
								{
									$showLabel = count($openOrderRelArrays) == 1 || (count($openOrderRelArrays) > 1 && $index == 0);
									$openOrderRel = $eachOpenOrderRel['openOrderRel']; 
									$OpenOrderRelModel->unit_price = empty($openOrderRel['unit_price']) ? NULL : $openOrderRel['unit_price'];
									echo $form->field($OpenOrderRelModel, "unit_price")
											->label($showLabel ? ('Price ($)'.($openOrderRel['need_attention'] ? "<span style='color: red;'> - Need Attention</span>" : '')) : false)
											->textInput([
												'class'=>'form-control primary', 
												'data-product_id'=>$productId, 
												'id'=>'primary-price-'.$openOrderRel['open_order_rel_id'], 
												'data-open_order_rel_id'=>$openOrderRel['open_order_rel_id']
											]);
								}
								echo "<span id='price-".$productId."'></span>";
							?>
						</td>
						<td>
							<?php 
								foreach($openOrderRelArrays AS $index => $eachOpenOrderRel) 
								{
									$showLabel = count($openOrderRelArrays) == 1 || (count($openOrderRelArrays) > 1 && $index == 0);
									$openOrderRel = $eachOpenOrderRel['openOrderRel']; 
									$OpenOrderRelModel->subtotal = $openOrderRel['qty']*$openOrderRel['unit_price'];
									$totalQty += $openOrderRel['qty'];
									$total += $OpenOrderRelModel->subtotal;
									echo $form->field($OpenOrderRelModel, "subtotal")
											->label($showLabel ? 'Subtotal ($)' : false)
											->textInput([
												'maxlength' => true, 
												'disabled'=>'disabled', 
												'id'=>'primary-subtotal-'.$openOrderRel['open_order_rel_id'], 
												'data-product_id'=>$productId, 
												'data-open_order_rel_id'=>$openOrderRel['open_order_rel_id']
											]);
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
					<th><strong><?= "$".$total ?></strong></th>
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

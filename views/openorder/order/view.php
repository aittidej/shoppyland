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
				<?php $product = $openOrderRel['product']; ?>
					<tr>
						<td width="25%" rowspan="2">
							<?= Html::img($product->firstImage, ['width'=>'100%']); ?>
						</td>
						<td colspan="3">
							<?= "<h4><strong>".Html::a($product->title, ['/product/view', 'id'=>$product->product_id], ['target'=>'_blank'])."</strong><br>Model #".$product->model."<br>UPC: ".$product->upc."</h4>"; ?>
						
						
						</td>
					</tr>
					<tr>
						<td width="25%">
							<?= $form->field($openOrderRel, "[$index]qty")->textInput(['type' => 'number']) ?>
						</td>
						<td width="25%">
							<?php
								if(empty($openOrderRel->unit_price))
									$openOrderRel->unit_price = 0;
								echo $form->field($openOrderRel, "[$index]unit_price")->label('Price ($)')->textInput(['type' => 'number', 'class'=>'form-control price-field', 'id'=>$index]); 
							?>
						</td>
						<td>
							<?php
								$openOrderRel->subtotal = $openOrderRel->qty*$openOrderRel->unit_price;
								$totalQty += $openOrderRel->qty;
								$total += $openOrderRel->subtotal;
								echo $form->field($openOrderRel, "[$index]subtotal")->label('Subtotal ($)')->textInput(['maxlength' => true, 'disabled'=>'disabled']);
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
			<center><?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?></center>
		</div>
	
	<?php ActiveForm::end(); ?>

</div>

<?php 
$this->registerJs("
$('.qty-field').keypress(function (e) {
	var keyCode = e.keyCode || e.which;
	if (keyCode == 9 || keyCode == 13) { 
		e.preventDefault(); 
		var vidID = parseInt(this.getAttribute('id'));
		$('#'+(vidID+1)).focus();
	}
});
$('.price-field').keypress(function (e) {
	var keyCode = e.keyCode || e.which;
	if (keyCode == 9 || keyCode == 13) { 
		e.preventDefault(); 
		var vidID = parseInt(this.getAttribute('id'));
		$('#'+(vidID+1)).focus();
	}
});
", View::POS_READY);
?>

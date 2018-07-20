<?php

use yii\web\View;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $openOrder app\models\OpenOrder */

$user = $openOrder->user;
$this->title = $user->name."'s Report - Lot #".$openOrder->lot_number;
$this->params['breadcrumbs'][] = ['label' => 'Open Orders', 'url' => ['/openorder/order/index']];
$this->params['breadcrumbs'][] = $this->title;
$total = $subtotal = $totalQty = 0;
?>
<style>
table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

td, th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
}

tr:nth-child(even) { background-color: #dddddd; }
</style>

<div class="open-order-view">
	<div class='col-sm-9'>
		<h1><?= Html::encode($this->title) ?></h1>
	</div>
	<div class='col-sm-3'><br>
		<?= Html::a('<i class="glyphicon glyphicon-plus"></i> Add More Items', ['/openorder/order/add-items', 'id'=>$openOrder->open_order_id], ['class' => 'btn btn-success']) ?>
		<?= Html::a('Edit Pricing', ['/openorder/order/view', 'id'=>$openOrder->open_order_id], ['class' => 'btn btn-info']) ?>
	</div>
		
	<div class='col-sm-12'>
		<table class="tftable" border="0">
			<tr>
				<th>Qty</th>
				<th>Unit Price</th>
				<th>Sub Total</th>
			</tr>
			<?php foreach($openOrderRels AS $index => $openOrderRel) { ?>
				<?php 
					$product = $openOrderRel['product'];
					$totalQty += $openOrderRel->qty;
					$subtotal += $openOrderRel->subtotal;
				?>
				<tr>
					<td><?= $openOrderRel->qty ?></td>
					<td><?= $openOrderRel->unit_price ?></td>
					<td><?= $openOrderRel->subtotal ?></td>
				</tr>
			<?php } ?>
			<tr>
				<th style='text-align: center;'>Total Qty: <?= $totalQty ?></th>
				<th style='text-align: right;'>
					Shipping Cost:<br>
					Subtotal:<hr>
					Total (USD): <br>
					Total (Baht): 
				</th>
				<th>
					<?php $total += $subtotal+$openOrder->shipping_cost; ?>
					$<?= number_format($openOrder->shipping_cost, 2) ?><br>
					$<?= number_format($subtotal, 2) ?><hr>
					$<?= number_format($total, 2) ?><br>
					<?= ceil($total*$user->exchange_rate) ?> &#3647;
				</th>
			</tr>
		</table>
	</div>

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

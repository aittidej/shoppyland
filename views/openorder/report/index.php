<?php

use yii\web\View;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $openOrder app\models\OpenOrder */

$user = $openOrder->user;
$lot = $openOrder->lot;
$this->title = $user->name."'s Report - Lot #".$lot->lot_number;
$this->params['breadcrumbs'][] = ['label' => 'Open Orders', 'url' => ['/openorder/order/index']];
$this->params['breadcrumbs'][] = $this->title;
$total = $subtotal = $totalQty = $tax = 0;
?>
<div class="open-order-view">
	<div class='col-sm-6'>
		<h1><?= Html::encode($this->title) ?></h1>
	</div>
	<div class='col-sm-6'><br>
		<?= Html::a('Print', 'javascript:void(0);', ['class' => 'btn btn-primary', 'id'=>'print', 'style'=>'float: right;']) ?> 
		<?= Html::a('Edit Pricing', ['/openorder/order/view', 'id'=>$openOrder->open_order_id], ['class' => 'btn btn-info', 'style'=>'float: right;']) ?> 
		<?= Html::a('<i class="glyphicon glyphicon-pencil"></i> Update Order', ['/openorder/order/update', 'id'=>$openOrder->open_order_id], ['class' => 'btn btn-warning', 'style'=>'float: right;']) ?> 
		<?= Html::a('<i class="glyphicon glyphicon-plus"></i> Add More Items', ['/openorder/order/add-items', 'id'=>$openOrder->open_order_id], ['class' => 'btn btn-success', 'style'=>'float: right;']) ?> 
	</div>
		
	<div class='col-sm-12' id="printablediv">
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

			tr:nth-child(even) { background-color: #dee7e8; }
		</style>
		<table class="tftable" border="0">
			<tr>
				<th>Item(s)</th>
				<th>Qty</th>
				<th>Unit Price</th>
				<th>Unit Subtotal</th>
			</tr>
			<?php foreach($openOrderRels AS $index => $openOrderRel) { ?>
				<?php 
					$product = $openOrderRel['product'];
					$brand = $product->brand;
					$totalQty += $openOrderRel->qty;
					$subtotal += $openOrderRel->subtotal;
					$tax += $openOrderRel->subtotal*0.1;
				?>
				<tr>
					<td width="40%"><?= $brand->title.' - '.$product->upc.' <strong>('.$product->model.')</strong>'; ?></td>
					<td><?= $openOrderRel->qty ?></td>
					<td>$<?= $openOrderRel->unit_price ?></td>
					<td>$<?= $openOrderRel->subtotal ?></td>
				</tr>
			<?php } ?>
			<tr>
				<th colspan="2" style='text-align: right;'>Total Items: <?= $totalQty ?></th>
				<th style='text-align: right;'>
					Additional Cost:<br>
					Shipping Cost:<br>
					Tax:<hr>
					Subtotal:<hr>
					Total (USD): <br>
					Total (Baht): 
				</th>
				<th>
					<?php $total += $subtotal+$tax; ?>
					$<?= number_format($openOrder->additional_cost, 2) ?><br>
					<?= number_format($openOrder->shipping_cost, 2) ?> ฿<br>
					$<?= number_format($tax, 2) ?><hr>
					$<?= number_format($subtotal, 2) ?><hr>
					$<?= number_format($total, 2) ?><br>
					<?= number_format(ceil($total*$user->exchange_rate+$openOrder->shipping_cost)) ?> &#3647;
				</th>
			</tr>
		</table>
	</div>

</div>

<?php 
$this->registerJs("

$('#print').click(function (e) {
	var myWindow = window.open('".Yii::$app->getUrlManager()->createUrl(['openorder/report/print', 'id'=>$openOrder->open_order_id])."', '', 'width=600, height=600, scrollbars=1');
});

", View::POS_READY);
?>

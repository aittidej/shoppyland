<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $openOrder app\models\OpenOrder */

$user = $openOrder->user;
$this->title = $user->name."'s Report - Lot #".$openOrder->lot_number;
$total = $subtotal = $totalQty = $tax = 0;
?>
<div class="open-order-view" id="printablediv">
	<div class='col-sm-12'><h2><?= Html::encode($this->title) ?></h2></div>
	<div class='col-sm-12'>
		<style>
			table {
				font-family: arial, sans-serif;
				font-size: 11px;
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
					<?php $total += $subtotal+$tax+$openOrder->shipping_cost; ?>
					$<?= number_format($openOrder->additional_cost, 2) ?><br>
					$<?= number_format($openOrder->shipping_cost, 2) ?><br>
					$<?= number_format($tax, 2) ?><hr>
					$<?= number_format($subtotal, 2) ?><hr>
					$<?= number_format($total, 2) ?><br>
					<?= number_format(ceil($total*$user->exchange_rate)) ?> &#3647;
				</th>
			</tr>
		</table>
	</div>
</div>

<script>
//$(function() {
	window.print();
//});
</script>

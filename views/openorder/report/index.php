<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $openOrder app\models\OpenOrder */

$taxRate = 0.08;
$user = $openOrder->user;
$isUsd = $user->currency_base == "USD";
$laborChargePrice = empty($user->labor_charge_json) ? [] : $user->labor_charge_json;
$shippingChargePrice = empty($user->shipping_charge_json) ? [] : $user->shipping_charge_json;
$lot = $openOrder->lot;
$this->title = $user->name."'s Invoice - Lot #".$lot->lot_number;
if(!$print) {
	$this->params['breadcrumbs'][] = ['label' => 'Open Orders', 'url' => ['/openorder/order/index']];
	$this->params['breadcrumbs'][] = $this->title;
}
$laborCount = [];
foreach($laborChargePrice AS $price)
	$laborCount[$price] = 0;
$total = $subtotal = $totalQty = $tax = $laborCost = 0;

$shippingText = '';
$shippingCost = $openOrder->shipping_cost;
if(empty($shippingCost) && !empty($shippingChargePrice) && !empty($openOrder->total_weight))
{
	if(empty($shippingChargePrice['cut_of_kg']) || $openOrder->total_weight >= $shippingChargePrice['cut_of_kg'])
	{
		$shippingCost = $openOrder->total_weight*$shippingChargePrice['tier'][1]+$openOrder->number_of_box*$shippingChargePrice['thai_shipping_cost'];
		$shippingText = "(".$openOrder->total_weight." kg x ".$shippingChargePrice['tier'][1].") + (".$openOrder->number_of_box." x ".$shippingChargePrice['thai_shipping_cost'].") = ".number_format($shippingCost, 2)." ฿";
	}
	else
	{
		$shippingCost = $openOrder->total_weight*$shippingChargePrice['tier'][2]+$openOrder->number_of_box*$shippingChargePrice['thai_shipping_cost'];
		$shippingText = "(".$openOrder->total_weight." kg x ".$shippingChargePrice['tier'][2].") + (".$openOrder->number_of_box." x ".$shippingChargePrice['thai_shipping_cost'].") = ".number_format($shippingCost, 2)." ฿";
	}
}

$link = "http://shoppylandbyhoney.com/index.php/openorder/client/preview?token=".$openOrder->token;
?>
<div class="open-order-view">
	<div class='col-sm-6'>
		<h1><?= Html::encode($this->title) ?></h1>
		<?php if($print && empty($client)) { ?>
			<center><h5>view this email on a <a href="<?= $link ?>" target="_blank">web browser</a></h5></center>
		<?php } ?>
	</div>
	<div class='col-sm-6'>
		<?php if(!$print) { ?>
			<br>
			<?= Html::a('Print', 'javascript:void(0);', ['class' => 'btn btn-primary', 'id'=>'print', 'style'=>'float: right;']) ?> 
			<?= Html::a('Email', ['/openorder/report/email', 'id'=>$openOrder->open_order_id], ['class' => 'btn btn-default', 'target'=>'_blank', 'style'=>'float: right;']) ?> 
			<?= Html::a('Edit Pricing', ['/openorder/order/view', 'id'=>$openOrder->open_order_id], ['class' => 'btn btn-info', 'style'=>'float: right;']) ?> 
			<?= Html::a('<i class="glyphicon glyphicon-pencil"></i> Update Order', ['/openorder/order/update', 'id'=>$openOrder->open_order_id], ['class' => 'btn btn-warning', 'style'=>'float: right;']) ?> 
			<?= Html::a('<i class="glyphicon glyphicon-plus"></i> Add More Items', ['/openorder/order/add-items', 'id'=>$openOrder->open_order_id], ['class' => 'btn btn-success', 'style'=>'float: right;']) ?> 
		<?php } ?>
	</div>
	
	<?php if(!empty($openOrder->note)) { ?>
		<div class='col-sm-12'><h4>Note: <?= $openOrder->note; ?></h4></div>
	<?php } ?>
	<div class='col-sm-12' id="printablediv">
		<table class="tftable" border="0" style="font-family: arial, sans-serif;font-size: 16px;border-collapse: collapse;width: 100%;">
			<tr>
				<th style="border: 1px solid #dddddd;text-align: left;padding: 8px;"></th>
				<th style="border: 1px solid #dddddd;text-align: left;padding: 8px;">Item(s)</th>
				<th style="border: 1px solid #dddddd;text-align: left;padding: 8px;">Qty</th>
				<th style="border: 1px solid #dddddd;text-align: left;padding: 8px;">Unit Price</th>
				<th style="border: 1px solid #dddddd;text-align: left;padding: 8px;">Unit Subtotal</th>
			</tr>
			<?php foreach($openOrderRels AS $index => $openOrderRel) { ?>
				<?php 
					$tempSub = $openOrderRel->unit_price*$openOrderRel->qty;
					$product = $openOrderRel['product'];
					$brand = $product->brand;
					$totalQty += $openOrderRel->qty;
					$subtotal += $tempSub;
					$tax += $tempSub*$taxRate;
					$brandTitle = empty($brand->title) ? '' : $brand->title.' - ';
					
					if(!empty($product->size) && !empty($laborChargePrice[$product->size]))
					{
						$laborCost += $laborChargePrice[$product->size]*$openOrderRel->qty;
						$laborCount[$laborChargePrice[$product->size]] += $openOrderRel->qty;
					}
				?>
				<tr style='<?= empty($index%2) ? "background-color: #dee7e8;" : "" ?>'>
					<td width="15%" style="border: 1px solid #dddddd;text-align: left;padding: 8px;">
						<?php
							$imagePath = $product->image_path;
							if(!empty($imagePath[0]))
							{
								if (strpos($imagePath[0], 'http') !== false)
									$image = $imagePath[0];
								else
									$image = Url::base(true) .'/'. $imagePath[0];
							}
							echo Html::img($image, ['width'=>'100%']);
						?>
					</td>
					<td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">
						<?php 
							$headerText = $brandTitle;
							$headerText .= $print ? $product->upc : Html::a($product->upc, ['/product/update', 'id'=>$product->product_id], ['target'=>'_blank']);
							$headerText .= empty($product->model) ? '' : ' <strong>('.$product->model.')</strong> ';
							$headerText .= (isset($laborChargePrice[$product->size]) ? " [$".$laborChargePrice[$product->size]."]" : '');
							$headerText .= '<br>'.$product->cleanTitle;
							echo $headerText;
							if(!$print && $isUsd)
							{
								echo "<br>".Html::a(' &nbsp;&nbsp;&nbsp;$4&nbsp;&nbsp;&nbsp; ', ['/product/pick-size', 'id'=>$product->product_id, 'size'=>'S'], ['target'=>'_blank']).
									Html::a(' &nbsp;&nbsp;&nbsp;$5&nbsp;&nbsp;&nbsp; ', ['/product/pick-size', 'id'=>$product->product_id, 'size'=>'M'], ['target'=>'_blank']).
									Html::a(' &nbsp;&nbsp;&nbsp;$6&nbsp;&nbsp;&nbsp; ', ['/product/pick-size', 'id'=>$product->product_id, 'size'=>'L'], ['target'=>'_blank']).
									Html::a(' &nbsp;&nbsp;&nbsp;$10&nbsp;&nbsp;&nbsp; ', ['/product/pick-size', 'id'=>$product->product_id, 'size'=>'XL'], ['target'=>'_blank']);
							}
						?>
					</td>
					<td style="border: 1px solid #dddddd;text-align: left;padding: 8px;"><?= $openOrderRel->qty ?></td>
					<td width="175px" style="border: 1px solid #dddddd;text-align: left;padding: 8px;">
						<?php
							if($isUsd)
								echo "$".(empty($openOrderRel->unit_price) ? '0.00' : $openOrderRel->unit_price);
							else
								echo (empty($openOrderRel->unit_price) ? '0.00' : $openOrderRel->unit_price)." &#3647;";
						?>
					</td>
					<td width="175px" style="border: 1px solid #dddddd;text-align: left;padding: 8px;">
						<?php
							if($isUsd)
								echo "$".number_format($tempSub, 2);
							else
								echo number_format($tempSub, 2)." &#3647;";
						?>
					</td>
				</tr>
			<?php } ?>
			<tr>
				<th style="border: 1px solid #dddddd;text-align: left;padding: 8px;">
					<?php if(!empty($laborCount) || !empty($openOrder->remark)) { ?>
					<h4>Remark: </h4>
					<?php
						if(!empty($openOrder->remark))
							echo $openOrder->remark;
						else
						{
							foreach($laborCount AS $price=>$itemNumb)
							{
								if(!empty($itemNumb))
									echo "$".$price." x ".$itemNumb."<br>";
							}
						}
					}
					?>
				</th>
				<th colspan="2" style="text-align: right;border: 1px solid #dddddd;padding: 8px;">
					Total Items: <?= $totalQty ?><br>
					<p><?= $shippingText //$openOrder->shipping_explanation ?></p>
				</th>

				<?php if($isUsd) { ?>
					<?php if($print) { ?>
						<th colspan="2" style="text-align: left;border: 1px solid #dddddd;padding: 8px;">
							<?php 
								$labor = empty($openOrder->additional_cost) ? $laborCost : $openOrder->additional_cost;
								$total = $subtotal+$tax+$labor; 
							?>
							<p><i>Subtotal:</i> $<?= number_format($subtotal, 2) ?></p>
							<p><i>Tax:</u> $<?= number_format($tax, 2) ?></p>
							<p><i>Labor:</u> $<?= number_format($labor, 2) ?></p>
							<p><i>Total ($):</u> $<?= number_format($total, 2) ?></p>
							<?php 
							if($user->payment_method == 'Baht') 
								echo "<p><i>Subtotal (x ".number_format($user->exchange_rate)."):</u> ".number_format($total*$user->exchange_rate, 2)."&#3647;</p><hr>"; 
							?>
							<p><i>Shipping Cost:</u> <?= number_format($shippingCost, 2) ?>&#3647;</p>
							<?php 
								if($user->payment_method == 'Baht')
								{
									echo "<p style='color:green;'><i>Total (&#3647;):</i> ".number_format($total*$user->exchange_rate+$shippingCost, 2)."&#3647;</p>";
									echo "<p style='color:red;'>Deposit (50%): $".number_format(($total*$user->exchange_rate+$shippingCost)/2, 2)."&#3647;</p>";
								}
							?> 
						</th>
					<?php } else { ?>
						<th style="text-align: right;border: 1px solid #dddddd;padding: 8px;">
							Subtotal:<br>
							Tax:<br>
							Labor:<br>
							Total ($): <br>
							<?php if($user->payment_method == 'Baht') echo "Subtotal (x ".number_format($user->exchange_rate)."):<hr>"; ?>
							Shipping Cost:<br>
							<?php if($user->payment_method == 'Baht') echo "Total (&#3647;): <br>"; ?>
							<span style="color:red;">Deposit (50%):</span>
						</th>
						<th style="text-align: left;border: 1px solid #dddddd;padding: 8px;">
							<?php 
								$labor = empty($openOrder->additional_cost) ? $laborCost : $openOrder->additional_cost;
								$total = $subtotal+$tax+$labor; 
							?>
							$<?= number_format($subtotal, 2) ?><br>
							$<?= number_format($tax, 2) ?><br>
							$<?= number_format($labor, 2) ?><br>
							$<?= number_format($total, 2) ?><br>
							<?php if($user->payment_method == 'Baht') echo number_format($total*$user->exchange_rate, 2)."&#3647;<hr>";  ?>
							<?= number_format($shippingCost, 2) ?>&#3647;<br>
							<?php 
								if($user->payment_method == 'Baht')
								{
									echo number_format($total*$user->exchange_rate+$shippingCost, 2)."&#3647;<br>";
									echo '<span style="color:red;">$'.number_format(($total*$user->exchange_rate+$shippingCost)/2, 2)."&#3647;</span>";
								}
								else
									echo '<span style="color:red;">$0.00</span>';
							?> 
						</th>
					<?php } ?>
				<?php } else { ?>
					<th style="text-align: right;border: 1px solid #dddddd;padding: 8px;">
						<p>Total (&#3647;):</p>
						<p style="color:red;">Deposit (50%):</p>
					</th>
					<th style="text-align: left;border: 1px solid #dddddd;padding: 8px;">
						<p><?= number_format($subtotal, 2) ?>&#3647;</p>
						<p><?= '<span style="color:red;">$'.number_format($subtotal/2, 2)."&#3647;</span>" ?></p>
					</th>
				<?php } ?>
			</tr>
		</table>
	</div>
</div>

<?php 
if($print && empty($client))
	echo "<script>window.print();</script>";
else
{
	$this->registerJs("
		$('#print').click(function (e) {
			var myWindow = window.open('".Yii::$app->getUrlManager()->createUrl(['openorder/report/print', 'id'=>$openOrder->open_order_id])."', '', 'width=600, height=600, scrollbars=1');
		});
	", View::POS_READY);
}
?>

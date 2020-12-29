<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $openOrder app\models\OpenOrder */

$missingSomething = $missingLabor = false;
$missing = [];
$taxRate = 0.08;
$user = $openOrder['user'];
$lot = $openOrder['lot'];
$isUsd = empty($openOrder->currency_base) ? $user->currency_base == "USD" : $openOrder->currency_base == "USD";
$paymentMethod = empty($openOrder->payment_method) ? $user->payment_method : $openOrder->payment_method;
$exchangeRate = empty($openOrder->exchange_rate) ? $user->exchange_rate : $openOrder->exchange_rate;
$link = "http://admin.shoppylandbyhoney.com/index.php/openorder/client/preview?token=".$openOrder->token;
$laborChargePrice = empty($user->labor_charge_json) ? [] : $user->labor_charge_json;
$shippingChargePrice = empty($user->shipping_charge_json) ? [] : $user->shipping_charge_json;
$this->title = $user->name."'s Invoice - Lot #".$lot->lot_number;
if(!$print) {
	$this->params['breadcrumbs'][] = ['label' => 'Open Orders', 'url' => ['/openorder/order/index']];
	$this->params['breadcrumbs'][] = $this->title;
}
$laborCount[0] = 0;
/*foreach($laborChargePrice AS $price)
	$laborCount[$price] = 0;*/
	
$total = $subtotal = $totalQty = $tax = $laborCost = $shippingTier = $count = 0;
$shippingText = '';
$shippingCost = $openOrder->shipping_cost;
$shippingCostUsd = $openOrder->shipping_cost_usd;
if($isUsd && empty($shippingCost) && !empty($shippingChargePrice) && !empty($openOrder->total_weight))
{
	if(empty($shippingChargePrice['cut_of_kg']))
		$shippingTier = $shippingChargePrice['tier'][1];
	else
	{
		foreach($shippingChargePrice['cut_of_kg'] AS $i=>$cut_of_kg)
		{
			if($cut_of_kg > $openOrder->total_weight) {
				$shippingTier = $shippingChargePrice['tier'][count($shippingChargePrice['tier'])-$count];
				break;
			}
			$count++;
		}
		
		if(empty($shippingTier))
			$shippingTier = $shippingChargePrice['tier'][1];
	}
	
	$shippingCost = $openOrder->total_weight*$shippingTier+$openOrder->number_of_box*$shippingChargePrice['thai_shipping_cost'];
	$shippingText = "(".$openOrder->total_weight." kg x ".$shippingTier.") + (".$openOrder->number_of_box." x ".$shippingChargePrice['thai_shipping_cost'].") = ".number_format($shippingCost, 2)." &#3647;";
}
?>
<style>
.summary th { border-right: 1px solid #dddddd; padding: 2.5px 5px 2.5px 0; }
.summary td { padding: 2.5px 0 2.5px 10px; }
</style>

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
			<?= Html::a("<i class='glyphicon glyphicon-print'></i> Print", 'javascript:void(0);', ['class' => 'btn btn-primary', 'id'=>'print', 'style'=>'float: right;']) ?> 
			<?= Html::a("<i class='glyphicon glyphicon-usd'></i> Edit Pricing", ['/openorder/order/view', 'id'=>$openOrder->open_order_id], ['class' => 'btn btn-info', 'style'=>'float: right;']) ?> 
			<?= Html::a('<i class="glyphicon glyphicon-pencil"></i> Update Order', ['/openorder/order/update', 'id'=>$openOrder->open_order_id], ['class' => 'btn btn-warning', 'style'=>'float: right;']) ?> 
			<?= Html::a('<i class="glyphicon glyphicon-plus"></i> Add More Items', ['/openorder/order/add-items', 'id'=>$openOrder->open_order_id], ['class' => 'btn btn-success', 'style'=>'float: right;']) ?> 
		<?php } ?>
	</div>
	
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
					if($openOrderRel->free_labor)
						$laborFee = 0;
					else if(!empty($openOrderRel->overwrite_labor))
						$laborFee = $openOrderRel->overwrite_labor;
					else if(!empty($laborChargePrice[$product->size]))
						$laborFee = $laborChargePrice[$product->size];
					else
					{
						$laborFee = NULL;
						//$missingSomething = true;
					}
					
					// remark display - product count
					if($openOrderRel->free_labor)
						$laborCount[0] += $openOrderRel->qty;
					else if(!empty($product->size) && !empty($laborFee))
					{
						if(!isset($laborCount[$laborFee]))
							$laborCount[$laborFee] = 0;
							
						$laborCost += $laborFee*$openOrderRel->qty;
						$laborCount[$laborFee] += $openOrderRel->qty;
					}
				?>
				<tr style='<?= empty($index%2) ? "background-color: #dee7e8;" : "" ?>'>
					<td width="15%" style="border: 1px solid #dddddd;text-align: left;padding: 8px;">
						<?= Html::img($product->firstImage, ['width'=>'100%']); ?>
					</td>
					<td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">
						<?php 
							if(empty($laborFee) && empty($openOrderRel->free_labor))
								$missingLabor = true;
							$headerText = $brandTitle;
							$headerText .= $print ? $product->upc : Html::a($product->upc, ['/product/update', 'id'=>$product->product_id], ['target'=>'_blank']);
							$headerText .= empty($product->model) ? '' : ' <strong>('.$product->model.')</strong> ';
							$headerText .= "<span id='labor-fee-text-".$openOrderRel->open_order_rel_id."'>";
							if($isUsd)
								$headerText .= (!empty($laborFee) ? " [$".$laborFee."]" : ($openOrderRel->free_labor ? '[FREE]' : '[N/A]'));
							$headerText .= "</span>";
							$headerText .= '<br>'.$product->cleanTitle;
							echo $headerText;
							if(!$print && $isUsd)
							{
								echo "<br><br><label>Labor Cost ($): </label>";
								echo Html::dropDownList('labor_cost', $laborFee, ['Free','$1','$2','$3','$4','$5','$6','$7','$8','$9','$10'], 
																[
																	'placeholder'=>'-- Select One --', 
																	'class'=>'form-control labor-cost-setting', 
																	'data-product_id'=>$product->product_id,
																	'data-open_order_rel_id'=>$openOrderRel->open_order_rel_id,
																]);
								
								echo Html::textInput('reference', $openOrderRel->reference, 
																	[
																		'class' => 'form-control reference',
																		'data-product_id'=>$product->product_id,
																		'data-open_order_rel_id'=>$openOrderRel->open_order_rel_id,
																		'placeholder'=>'Referencen'
																	]);
							}
							else
								echo "<p style='font-size: 11.5px;color: gray;'>".$openOrderRel->reference."</p>";
						?>
					</td>
					<td style="border: 1px solid #dddddd;text-align: left;padding: 8px;"><?= $openOrderRel->qty ?></td>
					<td width="125px" style="border: 1px solid #dddddd;text-align: left;padding: 8px;">
						<?php
							if($openOrderRel->unit_price == NULL) {
								$missingSomething = true;
								$missing[] = $product->upc;
								echo "<!-- ".$product->upc." -->";
							}
							
							if($isUsd)
								echo "$".(empty($openOrderRel->unit_price) ? '0.00' : $openOrderRel->unit_price);
							else
								echo (empty($openOrderRel->unit_price) ? '0.00' : $openOrderRel->unit_price)." &#3647;";
						?>
					</td>
					<td width="125px" style="border: 1px solid #dddddd;text-align: left;padding: 8px;">
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
					<?php if($isUsd && (!empty($laborCount) || !empty($openOrder->remark))) { ?>
					<h4>Remark: </h4>
					<?php
						if(!empty($openOrder->remark))
							echo $openOrder->remark;
						else
						{
							asort($laborCount);
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
					# of Items: <?= $totalQty ?><br>
					<p><?= $shippingText ?></p>
					<?= $openOrder->shipping_explanation ?>
				</th>

				<?php if($isUsd) { ?>
					<?php 
						$labor = empty($openOrder->labor_cost) ? $laborCost : $openOrder->labor_cost;
						$additionalCost = empty($openOrder->additional_cost) ? 0 : $openOrder->additional_cost;
						$total = $subtotal+$tax+$labor+$additionalCost; 
					?>
					<?php if($print) { ?>
						<th colspan="2" style="text-align: left;border: 1px solid #dddddd;padding: 8px;">
							<p><i>Items Total:</i> $<?= number_format($subtotal, 2) ?></p>
							<p><i>Tax:</u> $<?= number_format($tax, 2) ?></p>
							<p><i>Labor:</u> $<?= number_format($labor, 2) ?></p>
							<?php
								if(!empty($additionalCost)) {
									if($additionalCost < 0)
										echo "<p><i>Additional Cost:</u> <i style='color: red;'>($".number_format(abs($additionalCost), 2).")</i></p>";
									else
										echo "<p><i>Additional Cost:</u> $".number_format($additionalCost, 2)."</p>";
								}
							?>
							<p><i>Subtotal ($):</u> $<?= number_format($total, 2) ?></p>
							<?php 
							if($paymentMethod == 'Baht') 
								echo "<p><i>Subtotal (x ".$exchangeRate."):</u> ".number_format($total*$exchangeRate, 2)."&#3647;</p>"; 
							?><hr>
							
							<?php
								if(!empty($shippingCostUsd))
									echo "<p><i>Shipping Cost:</u> $".number_format($shippingCostUsd, 2)."<p>";
								else
									echo "<p><i>Shipping Cost:</u> ".number_format($shippingCost, 2)."&#3647;</p>";
									
								if($paymentMethod == 'Baht')
								{
									echo "<p style='color:green;'><i>Total (&#3647;):</i> ".number_format($total*$exchangeRate+$shippingCost, 2)."&#3647;</p>";
									echo "<p style='color:red;'>Deposit (50%): $".number_format(($total*$exchangeRate+$shippingCost)/2, 2)."&#3647;</p>";
								}
								else
									echo "Total: $".number_format($total+$shippingCostUsd, 2)."<br>";
							?> 
						</th>
					<?php } else { ?>
						<th colspan="2" style="border: 1px solid #dddddd;padding: 8px;">
							<table class='summary'>
								<tr>
									<th>Items Total:</th>
									<td>$<?= number_format($subtotal, 2) ?></td>
								</tr>
								<tr>
									<th>Tax:</th>
									<td>$<?= number_format($tax, 2) ?></td>
								</tr>
								<tr>
									<th>Labor:</th>
									<td>$<?= number_format($labor, 2) ?></td>
								</tr>
								<tr>
									<th>Additional Cost:</th>
									<td>
									<?php
										if(!empty($additionalCost)) 
										{
											if($additionalCost < 0)
												echo "<i style='color: red;'>($".number_format(abs($additionalCost), 2).")</i>";
											else
												echo "$".number_format($additionalCost, 2);
										}
										else
											echo "$0.00";
									?>
									</td>
								</tr>
								<tr>
									<th>Subtotal ($):</th>
									<td>$<?= number_format($total, 2) ?></td>
								</tr>
								
								<?php 
									if($paymentMethod == 'Baht') 
									{
										echo "<tr>";
											
											echo "<th>Subtotal x ".$exchangeRate.":</th>";
											echo "<td>".number_format($total*$exchangeRate, 2)."&#3647;</td>";
										
										echo "</tr>";
									}
								?>
								
								<tr><td colspan="2"><hr></td></tr>
								
								<?php 
									if($paymentMethod == 'Baht') 
									{
										echo "<tr>";
											echo "<th>Shipping Cost:</th>";
											echo "<td>".number_format($shippingCost, 2)."&#3647;</td>";
										echo "</tr>";
										
										
										echo "<tr>";
											echo "<th>Total (&#3647;):</th>";
											echo "<td>".number_format($total*$exchangeRate+$shippingCost, 2)."&#3647;</td>";
										echo "</tr>";
										
										echo "<tr>";
											echo "<th><span style='color:red;'>Deposit (50%):</span></th>";
											echo '<td style="color:red;">'.number_format(($total*$exchangeRate+$shippingCost)/2, 2)."&#3647;</td>";
											
										echo "</tr>";
									}
									else
									{
										echo "<tr>";
											echo "<th>Shipping Cost:</th>";
											echo "<td>$".(empty($shippingCostUsd) ? "0.00" : number_format($shippingCostUsd, 2))."</td>";
										echo "</tr>";
										
										
										echo "<tr>";
											echo "<th>Total ($):</th>";
											echo "<td>$".number_format($total+$shippingCostUsd, 2)."</td>";
										echo "</tr>";
									}
								?>

								
							</table>
						</th>
					
					<!--
						<th style="text-align: right;border: 1px solid #dddddd;padding: 0 8px;">
							Items Total:<br>
							Tax:<br>
							Labor:<br>
							Additional Cost:<br>
							Subtotal ($): <br>
							<?php 
							if($paymentMethod == 'Baht') 
								echo "Subtotal (x ".$exchangeRate."):"; ?><hr>
							Shipping Cost:<br>
							<?= ($paymentMethod == 'Baht') ? "Total (&#3647;): " :  "Total ($): "; ?><br>
							<?= ($paymentMethod == 'Baht') ? "<span style='color:red;'>Deposit (50%):</span><br>" :  ""; ?>
						</th>
						<th style="text-align: left;border: 1px solid #dddddd;padding: 0 8px;">
							$<?= number_format($subtotal, 2) ?><br>
							$<?= number_format($tax, 2) ?><br>
							$<?= number_format($labor, 2) ?><br>
							<?php
								if(!empty($additionalCost)) 
								{
									if($additionalCost < 0)
										echo "<i style='color: red;'>($".number_format(abs($additionalCost), 2).")</i>";
									else
										echo "$".number_format($additionalCost, 2);
								}
								else
									echo "$0.00";
							?><br>
							$<?= number_format($total, 2) ?><br>
							
							
							<?php if($paymentMethod == 'Baht') echo number_format($total*$exchangeRate, 2)."&#3647;";  ?><hr>
							<?php 
								if($paymentMethod == 'Baht')
									echo number_format($shippingCost, 2)."&#3647;<br>";
								else
									echo "$".(empty($shippingCostUsd) ? "0.00" : number_format($shippingCostUsd, 2))."<br>";
								
								if($paymentMethod == 'Baht')
								{
									echo number_format($total*$exchangeRate+$shippingCost, 2)."&#3647;<br>";
									echo '<span style="color:red;">'.number_format(($total*$exchangeRate+$shippingCost)/2, 2)."&#3647;</span>";
								}
								else
									echo "$".number_format($total+$shippingCostUsd, 2)."<br>";
							?> 
						</th>
					-->
					<?php } ?>
				<?php } else { ?>
					<th colspan="2" style="border: 1px solid #dddddd;padding: 8px;">
						<table id="summary-baht-table" style="width:100%">
							<tr>
								<th>Total (&#3647;):</th>
								<td><?= number_format($subtotal, 2) ?>&#3647;</td>
							</tr>
							<tr>
								<th style="color:red;">Deposit (50%):</th>
								<td style="color:red;"><?= number_format($subtotal/2, 2)."&#3647;" ?></td>
							</tr>
						</table>
					</th>
				<?php } ?>
			</tr>
		</table>
	</div>
	<?php if(!empty($openOrder->note)) { ?>
		<div class='col-sm-12'><h4><?= $openOrder->note; ?></h4></div>
	<?php } ?>
	<?php
		if(!$isUsd) // if baht then no labor
			$missingLabor = false;
	?>
	<div class='col-sm-12'><br>
		<center>
			<?php if(!$print && !$missingSomething && !$missingLabor) { ?>
					<?= Html::a(
							"<i class='glyphicon glyphicon-send'></i> " . (empty($openOrder->invoice_sent) ? 'Submit & Email Invoice' : 'Resend Email Invoice'), 
							['/openorder/report/email', 'id'=>$openOrder->open_order_id], 
							[
								'class' => empty($openOrder->invoice_sent) ? 'btn btn-success' : 'btn btn-info', 
								'data' => [
									'confirm' => 'Are you sure you want to submit now?',
									'method' => 'POST',
								]
					]); ?> 
			<?php } else if($missingSomething || $missingLabor) { ?>
				<h3 style='color:red'>* Cannot submit at this time. <?= $missingLabor ? 'Labor' : 'Something' ?> is missing. Please double check.</h3>
				<?php 
					echo "<pre>";
					var_dump("missingSomething=".($missingSomething?1:0));
					var_dump("missingLabor=".($missingLabor?1:0));
					var_dump($missing);
					echo "</pre>"; 
				?>
			<?php } ?>
		</center>
	</div>
</div>

<?php 
if($print && empty($client))
	echo "<script>window.print();</script>";
else
{
	$this->registerJs("
		var id = ".$openOrder->open_order_id.";
		$('#print').click(function (e) {
			var myWindow = window.open('".Yii::$app->getUrlManager()->createUrl(['openorder/report/print', 'id'=>$openOrder->open_order_id])."', '', 'width=600, height=600, scrollbars=1');
		});
		
		$('.labor-cost-setting').change(function (e) {
			var product_id = $(this).data('product_id');
			var open_order_rel_id = $(this).data('open_order_rel_id');
			var fee = parseInt($(this).val());
			$.ajax({
				url: '" . Yii::$app->getUrlManager()->createUrl(['product/pick-size']) . "',
				type: 'POST',
				beforeSend: function(xhr, opts) {
					if(fee == 0)
						$('#labor-fee-text-'+open_order_rel_id).html(' [FREE]');
					else
						$('#labor-fee-text-'+open_order_rel_id).html(' [$'+fee+']');
				},
				data: { id:id, fee:fee, product_id:product_id, open_order_rel_id:open_order_rel_id },
				success: function(result) {
					if(!result) {
						$('#labor-fee-text-'+open_order_rel_id).html(' ');
					}
				},
				error: function(err) {
					console.log(err);
				}
			});
		});
		
		$('.reference').focusout(function (e) {
			var open_order_rel_id = $(this).data('open_order_rel_id');
			var reference = $(this).val();
			$.ajax({
				url: '" . Yii::$app->getUrlManager()->createUrl(['openorder/report/reference']) . "',
				type: 'POST',
				data: { reference:reference, open_order_rel_id:open_order_rel_id },
				success: function(result) {
					console.log(result);
				},
				error: function(err) {
					console.log(err);
				}
			});
		});
		
		
	", View::POS_READY);
}
?>

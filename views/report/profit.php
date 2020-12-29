<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use dosamigos\chartjs\ChartJs;
use kartik\range\RangeInput;
use yii\helpers\Url;

$this->title = 'Profit Report';

$totalPurchaseWithTax = $totalUnits = 0;
foreach($receipts AS $receipt)
{
	$totalPurchaseWithTax += $receipt->total;
	$totalUnits += $receipt->number_of_items;
}

$baseRate = empty($baseRate['buy_exchange_rate']) ? 34 : $baseRate['buy_exchange_rate'];
$tax = 7.75;
$ccCashBack = 0.01; // 1%
$bahtProfitPercentage = 0.12; // 12%
$bahtProfitMargin = 10; // $10

$totalCollectibleWithTaxOnlyWithExchange = $totalCollectibleOnlyWithExchange*(1+$tax/100);
$bahtClientProfit = ($totalCollectibleInBaht*$bahtProfitPercentage)/$exchangeRate;
//$bahtClientProfit = $totalBathQty*$bahtProfitMargin;
$rate = (1-($exchangeRate/$baseRate))+$ccCashBack;
$exchangeRateIncome = number_format($totalCollectibleWithTaxOnlyWithExchange*$rate,2, '.', '');
$weightProfitInUSD = number_format($weightProfitInBaht/$exchangeRate,2, '.', '');


?>
<style>

</style>
<div class='col-sm-12'>
	<?php $form = ActiveForm::begin(['method' => 'GET', 'action' => 'profit']); ?>
		<h1>
			Lot # <?= Html::dropDownList(
				'lotNumber', //name
				$lotNumber,  //select
				ArrayHelper::map($lots, 'lot_number', 'lot_number'), //items
				['onchange'=>'this.form.submit();'] //options
			  ); ?> - Profit Report
		</h1>
	<?php ActiveForm::end(); ?>
</div>


<div class='col-sm-6'>
	<label class="control-label">Sell Exchange Rate (฿)</label>
	<?php echo RangeInput::widget([
				'name' => 'sell',
				'value' => $exchangeRate,
				'options' => ['placeholder' => 'Sell Exchange Rate (฿)', 'id'=>'sell'],
				'html5Container' => ['style' => 'width:50%'],
				'html5Options' => ['min' => 28, 'max' => 36, 'step' => 0.05],
				'addon' => ['append' => ['content' => '฿ per $']]
			]);
	?>
</div>

<div class='col-sm-6'>
	<label class="control-label">Base Exchange Rate (฿)</label>
	<?php echo RangeInput::widget([
				'name' => 'base',
				'value' => $baseRate,
				'options' => ['placeholder' => 'Base Exchange Rate (฿)', 'id'=>'base'],
				'html5Container' => ['style' => 'width:50%'],
				'html5Options' => ['min' => 30, 'max' => 38, 'step' => 0.5],
				'addon' => ['append' => ['content' => '฿ per $']]
			]);
	?>
</div>

<div class='col-sm-12'><hr></div>

<div class='col-sm-12'>
	<div class='col-sm-5'>
		
		<h4><?= "Number of Items: ".$numberOfItems ?></h4>
		<h4><?= "Number of Boxes: ".$numberOfBox ?></h4>
		<h4><?= "Total Weight: ".$totalWeightKg." kg" ?></h4>
		<h4><?= "Total Purchased: $".number_format($totalPurchaseWithTax, 2) ?></h4>
		<h4><?= "Total Collectible from client with Tax: $<span id='totalCollectibleWithTax'></span>" ?></h4>
		<hr>
		<h4><?= "Labor Cost: $".number_format($laborCost, 2) ?></h4>
		<h4><?= "Weight Profit: $<span id='weightProfit'></span>" ?></h4>
		<h4><?= "Exchange Rate Profit: $<span id='exchangeRateIncome'></span>" ?></h4>
		<h4><?= "Baht Client Profit: $<span id='bahtClientProfit'></span>" ?></h4>
		<hr>
		<h4><?= "Net Income: $<span id='netIncome'></span>" ?></h4>

	</div>
	<div class='col-sm-7'>

		<?= ChartJs::widget([
				'type' => 'pie',
				'id' => 'structurePie',
				'options' => ['id' => 'profit_pie'],
				'data' => [
					'radius' =>  "90%",
					'labels' => ['Labor Cost', 'Weight Profit', 'Exchange Rate Profit', 'Baht Client Profit'], // Your labels
					'datasets' => [
						[
							'data' => [$laborCost, $weightProfitInUSD, $exchangeRateIncome, $bahtClientProfit], // Your dataset
							'label' => '',
							'backgroundColor' => ['#ADC3FF', '#FF9A9A', '#5FBA7D', '#AF7AC5'],
							'borderColor' =>  ['#fff', '#fff', '#fff', '#fff'],
							'borderWidth' => 1,
							'hoverBorderColor'=>["#999", "#999", "#999", "#999"],                
						]
					]
				],
				'clientOptions' => [
					'legend' => [
						'display' => true,
						'position' => 'bottom',
						'labels' => [
							'fontSize' => 14,
							'fontColor' => "#425062",
						]
					],
					'tooltips' => [
						'enabled' => true,
						'intersect' => true
					],
					'hover' => [
						'mode' => false
					],
					'maintainAspectRatio' => true,
				]
			]);
		?>

	</div>
</div>
<div class='col-sm-12'><hr></div>
<div class='col-sm-12'>
	<div class='col-sm-6'>
		<h1>Top 10 Most Sold Products</h1>
		<table class="table table-striped top-ten">
			<thead>
				<tr>
					<th scope="col" style='vertical-align: middle;text-align: center;'>#</th>
					<th scope="col"></th>
					<th scope="col" style='vertical-align: middle;text-align: center;'>Qty</th>
				</tr>
			</thead>
			<tbody>
				<?php
					usort($productsStats, function($a, $b) {
						return $b['qty'] <=> $a['qty'];
					});
					$count = 1;
					foreach($productsStats AS $product_id=>$productsStat)
					{
						if($count == 11) break;
						$qty = $productsStat['qty'];
						$product = $productsStat['product'];
						$imagePath = json_decode($product['image_path'], 2);
						$image = "http://www.topprintltd.com/global/images/PublicShop/ProductSearch/prodgr_default_300.png";
						if(!empty($imagePath[0]))
						{
							if (strpos($imagePath[0], 'http') !== false)
								$image = $imagePath[0];
							else
								$image = Url::base(true) .'/'. $imagePath[0];
						}
						
						echo "<tr><th scope='row' width='100px' style='vertical-align: middle;text-align: center;'>$count</th><td style='vertical-align: middle;text-align: center;'>".Html::img($image, ['width'=>'150px'])."</td><td width='200px' style='vertical-align: middle;text-align: center;'>$qty</td></tr>";
						$count++;
					}
				?>
			</tbody>
		</table>
	</div>
	
	<div class='col-sm-6'>
		<h1>Top 10 Profitable Products</h1>
		<table class="table table-striped top-ten">
			<thead>
				<tr>
					<th scope="col" style='vertical-align: middle;text-align: center;'>#</th>
					<th scope="col"></th>
					<th scope="col" style='vertical-align: middle;text-align: center;'>Cost</th>
				</tr>
			</thead>
			<tbody>
				<?php
					usort($productsStats, function($a, $b) {
						return $b['cost'] <=> $a['cost'];
					});
					$count = 1;
					foreach($productsStats AS $product_id=>$productsStat)
					{
						if($count == 11) break;
						$qty = $productsStat['qty'];
						$cost = $productsStat['cost'];
						$product = $productsStat['product'];
						$imagePath = json_decode($product['image_path'], 2);
						$image = "http://www.topprintltd.com/global/images/PublicShop/ProductSearch/prodgr_default_300.png";
						if(!empty($imagePath[0]))
						{
							if (strpos($imagePath[0], 'http') !== false)
								$image = $imagePath[0];
							else
								$image = Url::base(true) .'/'. $imagePath[0];
						}
						
						echo "<tr><th scope='row' width='100px' style='vertical-align: middle;text-align: center;'>$count</th><td style='vertical-align: middle;text-align: center;'>".Html::img($image, ['width'=>'150px'])."</td><td width='350px' style='vertical-align: middle;text-align: center;'>$".number_format($cost, 2)."<br>(with $qty items)</td></tr>";
						$count++;
					}
				?>
			</tbody>
		</table>
		
	
	
	
	</div>
</div>
	
<?php 

$this->registerJs("
	var baseRate = parseFloat(".$baseRate.");
	var exchangeRate = parseFloat(".$exchangeRate.");
	var bahtProfitPercentage = parseFloat(".$bahtProfitPercentage.");
	var laborCost = parseFloat(".$laborCost.");
	var tax = parseFloat(".$tax.");
	var ccCashBack = parseFloat(".$ccCashBack.");
	var numberOfItems = parseFloat(".$numberOfItems.");
	var totalBathQty = parseFloat(".$totalBathQty.");
	var bahtProfitMargin = parseFloat(".$bahtProfitMargin.");
	var totalCollectible = parseFloat(".$totalCollectible.");
	var totalCollectibleWithTaxOnlyWithExchange = parseFloat(".$totalCollectibleWithTaxOnlyWithExchange.");
	var totalCollectibleInBaht = parseFloat(".$totalCollectibleInBaht.");
	var weightProfitInBaht = parseFloat(".$weightProfitInBaht.");
	
	calculate(baseRate, exchangeRate);
	
	$('#sell').change(function (e) {
		var sell = $(this).val();
		var base = $('#base').val();
		calculate(base, sell);
	});
	
	$('#base').change(function (e) {
		var base = $(this).val();
		var sell = $('#sell').val();
		calculate(base, sell);
	});
	
	function calculate(base, sell)
	{
		var rate = (1-(sell/base))+ccCashBack;
		var totalCollectibleWithTax = totalCollectible*(1+tax/100)+(totalCollectibleInBaht/base);
		var bahtClientProfit = (totalCollectibleInBaht*bahtProfitPercentage)/sell;
		//var bahtClientProfit = totalBathQty*bahtProfitMargin;
		var exchangeRateIncome = totalCollectibleWithTaxOnlyWithExchange*rate;
		var weightProfitInUSD = weightProfitInBaht/sell;
		
		$('#totalCollectibleWithTax').html(totalCollectibleWithTax.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
		$('#weightProfit').html(weightProfitInUSD.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
		$('#exchangeRateIncome').html(exchangeRateIncome.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
		$('#bahtClientProfit').html(bahtClientProfit.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
		$('#netIncome').html((laborCost+exchangeRateIncome+bahtClientProfit+weightProfitInUSD).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
		
		chartJS_profit_pie.data.datasets[0].data[1] = weightProfitInUSD.toFixed(2);
		chartJS_profit_pie.data.datasets[0].data[2] = exchangeRateIncome.toFixed(2);
		chartJS_profit_pie.data.datasets[0].data[3] = bahtClientProfit.toFixed(2);
		chartJS_profit_pie.update();
	}

",View::POS_READY); ?>
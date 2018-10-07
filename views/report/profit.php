<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use dosamigos\chartjs\ChartJs;
use kartik\range\RangeInput;

$this->title = 'Profit Report';

$totalPurchaseWithTax = $totalUnits = 0;
foreach($receipts AS $receipt)
{
	$totalPurchaseWithTax += $receipt->total;
	$totalUnits += $receipt->number_of_items;
}

$baseRate = 34;
$exchangeRate = 31.8;
$laborFee = 6;
$tax = 7.75;
$ccCashBack = 0.01;

$laborCost = number_format($numberOfItems*$laborFee, 2, '.', '');
$totalCollectibleWithTax = $totalCollectible*(1+$tax/100);
$totalCollectibleWithTaxOnlyWithExchange = $totalCollectibleOnlyWithExchange*(1+$tax/100);

$rate = (1-($exchangeRate/$baseRate))+$ccCashBack;
$exchangeRateIncome = number_format($totalCollectibleWithTaxOnlyWithExchange*$rate,2, '.', '');
$weightProfitInUSD = number_format($weightProfitInBaht/$exchangeRate,2, '.', '');
?>
<style>

</style>
<div class='col-sm-12'>
	<h1>Lot #<?= $lot->lot_number ?> - Profit Report</h1>
</div>


<div class='col-sm-6'>
	<label class="control-label">Sell Exchange Rate (฿)</label>
	<?php echo RangeInput::widget([
				'name' => 'sell',
				'value' => $exchangeRate,
				'options' => ['placeholder' => 'Sell Exchange Rate (฿)', 'id'=>'sell'],
				'html5Container' => ['style' => 'width:50%'],
				'html5Options' => ['min' => 30, 'max' => 34, 'step' => 0.05],
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

<div class='col-sm-5'>
	
	<h4><?= "Number of Items: ".$numberOfItems ?></h4>
	<h4><?= "Total Purchased: $".number_format($totalPurchaseWithTax, 2) ?></h4>
	<h4><?= "Total Collectible with Tax: $".number_format($totalCollectibleWithTax, 2) ?></h4>
	<hr>
	<h4><?= "Labor Cost: $".number_format($laborCost, 2) ?></h4>
	<h4><?= "Weight Profit: $<span id='weightProfit'></span>" ?></h4>
	<h4><?= "Exchange Rate Profit: $<span id='exchangeRateIncome'></span>" ?></h4>
	<hr>
	<h4><?= "Net Income: $<span id='netIncome'></span>" ?></h4>

</div>
<div class='col-sm-7'>

	<?php echo ChartJs::widget([
			'type' => 'pie',
			'id' => 'structurePie',
			'options' => [
				'id' => 'profit_pie',
				//'height' => 200,
				//'width' => '100%',
			],
			'data' => [
				'radius' =>  "90%",
				'labels' => ['Labor Cost', 'Weight Profit', 'Exchange Rate Profit'], // Your labels
				'datasets' => [
					[
						'data' => [$laborCost, $weightProfitInUSD, $exchangeRateIncome], // Your dataset
						'label' => '',
						'backgroundColor' => ['#ADC3FF', '#FF9A9A', '#5FBA7D'],
						'borderColor' =>  ['#fff', '#fff', '#fff'],
						'borderWidth' => 1,
						'hoverBorderColor'=>["#999", "#999", "#999"],                
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

<?php 
$this->registerJs("
	var baseRate = parseFloat(".$baseRate.");
	var exchangeRate = parseFloat(".$exchangeRate.");
	var laborFee = parseFloat(".$laborFee.");
	var laborCost = parseFloat(".$laborCost.");
	var tax = parseFloat(".$tax.");
	var ccCashBack = parseFloat(".$ccCashBack.");
	var numberOfItems = parseFloat(".$numberOfItems.");
	var totalCollectibleWithTaxOnlyWithExchange = parseFloat(".$totalCollectibleWithTaxOnlyWithExchange.");
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
		var exchangeRateIncome = totalCollectibleWithTaxOnlyWithExchange*rate;
		var weightProfitInUSD = weightProfitInBaht/sell;
		
		$('#weightProfit').html(weightProfitInUSD.toFixed(2));
		$('#exchangeRateIncome').html(exchangeRateIncome.toFixed(2));
		$('#netIncome').html((laborCost+exchangeRateIncome+weightProfitInUSD).toFixed(2));
		
		chartJS_profit_pie.data.datasets[0].data[1] = weightProfitInUSD.toFixed(2);
		chartJS_profit_pie.data.datasets[0].data[2] = exchangeRateIncome.toFixed(2);
		chartJS_profit_pie.update();
	}

",View::POS_READY); ?>
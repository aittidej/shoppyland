<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Lot */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Add Discount';
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

tr:nth-child(even) {
    background-color: #dddddd;
}
</style>

<div class="discount-form">
	<div class="col-sm-12"><h1><?= $this->title ?></h1></div>
    <?php $form = ActiveForm::begin(); ?>
		<div class="col-sm-11">
			<?= Html::textInput('discounts', NULL, ['class' => 'form-control', 'id'=>'coupon', 'placeholder'=>'Example: put 60,20,10 for 60% + 20% + 10%']);  ?>
		</div>
		
		<div class="form-group col-sm-1">
			<?= Html::submitButton('Add', ['class' => 'btn btn-success']) ?>
		</div>

    <?php ActiveForm::end(); ?>
	
	<div class="col-sm-12">
		<h2>Existed Discounts</h2>
		<table>
			<tr>
				<th>Discount</th>
				<th>How to put it</th>
			</tr>
			<?php foreach($discountLists AS $discountList) { ?>
				<tr>
					<td><?= $discountList->title ?></td>
					<td><?= $discountList->howToPutIt ?></td>
				</tr>
			<?php } ?>
		</table>
	</div>
</div>

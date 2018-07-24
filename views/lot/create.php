<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\DiscountList;

/* @var $this yii\web\View */
/* @var $model app\models\Lot */

$this->title = 'Create Lot';
$this->params['breadcrumbs'][] = ['label' => 'Lots', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$discountLists = ArrayHelper::map(DiscountList::find()->where(['status'=>'1'])->orderby('title ASC')->all(), 'discount_list_id', 'title');
?>
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

	tr:nth-child(even) { background-color: #d2e6ea; }
</style>

<div class="lot-create">

    <h1><?= Html::encode($this->title) ?></h1>

	<?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'lot_number')->textInput() ?>
	
	<table class="tftable" border="0">
		<tr>
			<th>Image</th>
			<th>Discount</th>
			<th>Price</th>
			<th>Subtotal</th>
		</tr>
		<?php foreach($products AS $index => $product) { ?>
			<tr>
				<td width="15%"><?= Html::img($product->firstImage, ['width'=>'90%'])."<br># ".$product->model."<br>".$product->upc; ?></td>
				<td><?= Html::textInput('LotRel[price]['.$product->product_id.']', NULL, ['class' => 'form-control price', 'placeholder'=>'Price']);  ?></td>
				<td>
					<?= Html::radioList('LotRel[discount]['.$product->product_id.']', NULL, $discountLists, [
							'item' => function ($index, $label, $name, $checked, $value) {
								return '<label>'.Html::radio($name, $checked, ['value' => $value, 'class'=>'discount']).'  '.$label.'</label><br>';
							}
						]); ?>
				</td>
				<td><?= Html::textInput('LotRel[subtotal]['.$product->product_id.']', NULL, ['class' => 'form-control price', 'placeholder'=>'Subtotal']);  ?></td>
			</tr>
		<?php } ?>
	</table>
	
	<div class="clearfix"></div><br>
	
    <div class="form-group">
		<center><?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?></center>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php $this->registerJs("

	$('.discount').change(function (e) {
		
		alert('test');
		
	});

",View::POS_READY); ?>
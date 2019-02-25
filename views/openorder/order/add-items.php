<?php

use yii\web\View;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OpenOrder */

$user = $model->user;
$lot = $model->lot;
$this->title = 'Add Items for '.$user->name.' - Lot #'.$lot->lot_number;
$this->params['breadcrumbs'][] = ['label' => 'Open Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Pricing', 'url' => ['view', 'id'=>$model->open_order_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="open-order-create">

    <h1><?= Html::encode($this->title) ?></h1>

	<?php $form = ActiveForm::begin(); ?>

		<div class='col-sm-12 col-md-6 col-lg-6'>
			<?= $form->field($model, 'items')->label('Add by Barcode (UPC)')->textarea(['rows' => '20', 'id'=>'items-field']) ?>
			<h3>Number of items: <span id="number-of-item">0</span></h3>
		</div>
		
		<div class='col-sm-12 col-md-6 col-lg-6'>
			<?= $form->field($model, 'productIdList')->label('Add by Product ID')->textarea(['rows' => '20', 'id'=>'product-id-list-field']) ?>
			<h3>Number of items: <span id="number-of-product-id">0</span></h3>
		</div>

		<div class="form-group col-sm-12">
			<?= Html::submitButton('Add', ['class' => 'btn btn-success']) ?>
		</div>

	<?php ActiveForm::end(); ?>
	
</div>
<script>
document.getElementById('items-field').focus();
</script>
<?php 
	$this->registerJs("
		$('#items-field').bind('keypress keyup keydown', function (event) {
			var count = 0;
			var value = $(this).val();
			var lines = value.split('\\n');
			for( var i = 0; i < lines.length; i++)
			{
				if ( lines[i].length !== 0 ) {
					count++;
				}
			}
			$('#number-of-item').text(count);
			
			/*var keycode = event.keyCode || event.which;
			if(keycode == '13') {
				var lines = $(this).val().split('\\n').length;
				$('#number-of-item').text(lines);
			}*/
			
		});
		
		$('#product-id-list-field').bind('keypress keyup keydown', function (event) {
			var count2 = 0;
			var value2 = $(this).val();
			var lines2 = value2.split('\\n');
			for( var j = 0; j < lines2.length; j++)
			{
				if ( lines2[j].length !== 0 ) {
					count2++;
				}
			}
			$('#number-of-product-id').text(count2);
		});
		
		
		$.fn.enterKey = function (fnc) {
			return this.each(function () {
				$(this).keypress(function (ev) {
					var keycode = (ev.keyCode ? ev.keyCode : ev.which);
					if (keycode == '13') {
						fnc.call(this, ev);
					}
				})
			})
		}
	", View::POS_READY);
?>
<?php

use yii\web\View;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

use app\models\DiscountList;

/* @var $this yii\web\View */
/* @var $model app\models\Lot */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Update Lot #' . $model->lot_number . ' ('.(empty($model->user_id) ? 'All buyers' : $model->user->name).')';
$this->params['breadcrumbs'][] = ['label' => 'Lots', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lot_id, 'url' => ['view', 'id' => $model->lot_id]];
$this->params['breadcrumbs'][] = 'Update';

$discountLists = ArrayHelper::map(DiscountList::find()->where(['status'=>1])->orderby('title ASC')->all(), 'discount_list_id', 'title');
?>

<div class="lot-form">
	<h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>
	
		<div class='col-sm-12 col-md-6 col-lg-6'>
			<?= $form->field($model, 'discount_list_id')->label('Discount')->dropDownList($discountLists, ['prompt'=>'Select discount...', 'id'=>'discount']); ?>
		</div>	
		<div class='col-sm-12 col-md-6 col-lg-6'>
			<?= $form->field($model, 'price')->label('Price ($)')->textInput(['id'=>'price']) ?>
		</div>
		<div class='col-sm-12'>
			<center><?= Html::a('Add by Barcode & Edit Price', ['/lot/update', 'id'=>$model->lot_id], ['class' => 'btn btn-success']) ?></center>
		</div>
		<div class='col-sm-12'>
			<center>
			<?= $form->field($model, 'items')->label(false)->checkboxList($products, [
					'item' => function($index, $label, $name, $checked, $value) {
						$print = Html::img($label->firstImage, ['style'=>'width: 100px; padding: 3px;cursor: pointer;', 'title'=>$label->title])."<br>".$label->upc."<br>[".$label->model."]";
						echo "<span class='checkbox-".$label->product_id."'>".Html::checkbox($name, $checked, ['label' => $print, 'data-product_id'=>$label->product_id, 'style'=>'display:none;'])."</span>";
					}]);
			?>
			</center>
		</div>

    <?php ActiveForm::end(); ?>

</div>

<?php $this->registerJs("
	var lot_id = '".$model->lot_id."';
	$('input:checkbox').change(function (e) {
		var discount_id = $('#discount').val();
		var price = $('#price').val();
		var product_id = $(this).data('product_id');
		var isCheck = $(this).is(':checked') ? 1 : 0;
		$('.checkbox-'+product_id).hide(500);
		
		$.ajax({
			url: '" . Yii::$app->getUrlManager()->createUrl('lot/selected-product') . "',
			type: 'POST',
			data: { lot_id:lot_id, product_id:product_id, price:price, discount_id:discount_id, isCheck:isCheck },
			/*success: function(result) {
				//console.log(result);
			},*/
			error: function(err) {
				console.log(err);
			}
		});
	});

",View::POS_READY); ?>

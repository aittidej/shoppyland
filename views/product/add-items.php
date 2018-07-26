<?php

use yii\web\View;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Brand;

/* @var $this yii\web\View */
/* @var $model app\models\OpenOrder */

$this->title = 'Add Product using UPC';
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$brands = ArrayHelper::map(Brand::find()->where(['status'=>'1'])->orderby('title ASC')->all(), 'brand_id', 'title');
?>
<div class="open-order-create">

    <h1><?= Html::encode($this->title) ?></h1>

	<?php $form = ActiveForm::begin(); ?>

		<div class='col-sm-12 col-md-6 col-lg-6'>
			<?= $form->field($model, 'model')->textInput() ?>
			<?= $form->field($model, 'brand_id')->dropDownList($brands, ['prompt'=>'Select Brand...']); ?>
		</div>
		
		<div class='col-sm-12 col-md-6 col-lg-6'>
			<?= $form->field($model, 'items')->label('Barcode (UPC)')->textarea(['rows' => '20', 'id'=>'items-field']) ?>
		</div>

		<div class="form-group col-sm-12">
			<?= Html::submitButton('Add', ['class' => 'btn btn-success']) ?>
		</div>

	<?php ActiveForm::end(); ?>
	
</div>
<script>
document.getElementById('items-field').focus();
</script>
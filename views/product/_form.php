<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\file\FileInput;
use app\models\Brand;

/* @var $this yii\web\View */
/* @var $model app\models\Product */
/* @var $form yii\widgets\ActiveForm */

$brands = ArrayHelper::map(Brand::find()->where(['status'=>'1'])->orderby('title ASC')->all(), 'brand_id', 'title');
?>
<script src="http://malsup.github.com/jquery.form.js"></script> 

<div class="product-form">

	<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data'], 'method' => 'POST']); ?>

		<div class='col-sm-12'>
			<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
		</div>
		
		<div class='col-sm-3'>
			<?= $form->field($model, 'brand_id')->dropDownList($brands, ['prompt'=>'Select Brand...']); ?>
		</div>
		<div class='col-sm-3'>
			<?= $form->field($model, 'model')->textInput(['maxlength' => true]) ?>
		</div>
		<div class='col-sm-6'>
			<?= $form->field($model, 'upc')->label('Barcode (UPC)')->textInput(['maxlength' => true]) ?>
		</div>
		
		<div class='col-sm-1'>
			<?= $form->field($model, 'size')->dropDownList(['XXS'=>'XXS', 'XS'=>'XS', 'S'=>'S', 'M'=>'M', 'L'=>'L', 'XL'=>'XL', 'XXL'=>'XXL', 'XXXL'=>'XXXL'], ['prompt'=>'Select Size...']); ?>
		</div>
		<div class='col-sm-3'>
			<?= $form->field($model, 'color')->textInput() ?>
		</div>
		<div class='col-sm-3'>
			<?= $form->field($model, 'base_price')->textInput() ?>
		</div>
		<div class='col-sm-3'>
			<?= $form->field($model, 'dimension')->textInput() ?>
		</div>
		<div class='col-sm-2'>
			<?= $form->field($model, 'weight')->textInput() ?>
		</div>
		
		<div class='col-sm-12'>
			<?php //$form->field($upload, "image[]")->fileInput(['multiple' => true]); ?>
			<?= $form->field($upload, 'image[]')->widget(FileInput::classname(), [
																'options' => ['accept' => 'image/*', 'multiple' => true],
																'pluginOptions' => [
																	//'showPreview' => false,
																	//'showCaption' => true,
																	//'showRemove' => true,
																	'showUpload' => false
																]
															]); ?>
		</div>
		
		<?php //$form->field($model, 'weight')->textInput() ?>
		
		<?php //$form->field($model, 'category')->textInput() ?>

		<?php //$form->field($model, 'description')->textarea(['rows' => 6]) ?>

		<?php //$form->field($model, 'color')->textInput(['maxlength' => true]) ?>

		<?php //$form->field($model, 'size')->textInput(['maxlength' => true]) ?>

		<?php //$form->field($model, 'dimension')->textInput(['maxlength' => true]) ?>
		
    <div class="col-sm-12 form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

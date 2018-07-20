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

<div class="product-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data'], 'method' => 'POST']); ?>

    <?= $form->field($model, 'upc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'model')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'brand_id')->dropDownList($brands, ['prompt'=>'Select Brand...']); ?>

    <?= $form->field($model, 'base_price')->textInput() ?>

    <?= $form->field($model, 'category')->textInput() ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'weight')->textInput() ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'color')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'size')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'dimension')->textInput(['maxlength' => true]) ?>

	<?= $form->field($upload, "image[]")->fileInput(['multiple' => true]); ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

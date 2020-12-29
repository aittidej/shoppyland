<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\LotRel */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lot-rel-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'lot_id')->textInput() ?>

    <?= $form->field($model, 'product_id')->textInput() ?>

    <?= $form->field($model, 'discount_list_id')->textInput() ?>

    <?= $form->field($model, 'price')->textInput() ?>

    <?= $form->field($model, 'overwrite_total')->textInput() ?>

    <?= $form->field($model, 'creation_datetime')->textInput() ?>

    <?= $form->field($model, 'bought_date')->textInput() ?>

    <?= $form->field($model, 'total')->textInput() ?>

    <?= $form->field($model, 'bought_price')->textInput() ?>

    <?= $form->field($model, 'currency')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

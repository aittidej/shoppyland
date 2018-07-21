<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OpenOrder */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="open-order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'lot_number')->textInput() ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'number_of_box')->textInput() ?>

    <?= $form->field($model, 'total_weight')->textInput() ?>

    <?= $form->field($model, 'shipping_cost')->textInput() ?>
	
    <?= $form->field($model, 'additional_cost')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save & Continue', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

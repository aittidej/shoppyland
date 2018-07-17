<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OpenOrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="open-order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'open_order_id') ?>

    <?= $form->field($model, 'lot_number') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'creation_datetime') ?>

    <?= $form->field($model, 'number_of_box') ?>

    <?php // echo $form->field($model, 'total_weight') ?>

    <?php // echo $form->field($model, 'total_usd') ?>

    <?php // echo $form->field($model, 'total_baht') ?>

    <?php // echo $form->field($model, 'status') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

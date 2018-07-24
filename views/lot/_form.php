<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Lot */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lot-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'lot_number')->textInput() ?>

    <?= $form->field($model, 'creation_datetime')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

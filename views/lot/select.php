<?php

use yii\web\View;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Lot */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Update Lot #' . $model->lot_number . ' ('.(empty($model->user_id) ? 'All buyers' : $model->user->name).')';
$this->params['breadcrumbs'][] = ['label' => 'Lots', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lot_id, 'url' => ['view', 'id' => $model->lot_id]];
$this->params['breadcrumbs'][] = 'Update';
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

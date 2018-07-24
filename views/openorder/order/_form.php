<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\OpenOrder */
/* @var $form yii\widgets\ActiveForm */

$users = ArrayHelper::map(User::find()->where(['status'=>'1'])->orderby('name ASC')->all(), 'user_id', 'name');
?>

<div class="open-order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'lot_number')->textInput() ?>

	<?= $form->field($model, 'user_id')->dropDownList($users, ['prompt'=>'Select buyer...']); ?>

    <?= $form->field($model, 'number_of_box')->textInput() ?>

    <?= $form->field($model, 'total_weight')->textInput() ?>

    <?= $form->field($model, 'shipping_cost')->textInput() ?>
	
    <?= $form->field($model, 'additional_cost')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save & Continue', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

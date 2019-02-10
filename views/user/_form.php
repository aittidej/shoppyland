<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\SwitchInput;
use app\models\Role;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */

$roles = ArrayHelper::map(Role::find()->orderby('title ASC')->all(), 'role_id', 'title');
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'temp_password')->label('Password')->passwordInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'role_id')->dropDownList($roles, ['prompt'=>'Select Role...']); ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textarea(['rows' => 6]) ?>

	<?= $form->field($model, 'payment_method')->dropDownList(['Baht'=>'Baht', 'USD'=>'USD']); ?>
	
	<?= $form->field($model, 'currency_base')->dropDownList(['Baht'=>'Baht', 'USD'=>'USD']); ?>

	<?= $form->field($model, 'is_wholesale')->label('Wholesale')->widget(SwitchInput::classname(), [
						'pluginOptions' => [
							'onText' => 'Yes',
							'offText' => 'No',
							'onColor' => 'success',
							'offColor' => 'info',
						],
						/*'pluginEvents' => [
							'switchChange.bootstrapSwitch' => 'function(event,state) { toggleGroups(state); }',
						]*/
					]);  ?>

    <?= $form->field($model, 'exchange_rate')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

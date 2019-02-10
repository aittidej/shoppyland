<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\User;
use app\models\Lot;
use dosamigos\ckeditor\CKEditor;

/* @var $this yii\web\View */
/* @var $model app\models\OpenOrder */
/* @var $form yii\widgets\ActiveForm */

$lots = ArrayHelper::map(Lot::find()->orderby('lot_number DESC')->all(), 'lot_id', 'lotOwnerText');
$users = ArrayHelper::map(User::find()->where("status=1 AND role_id != 1")->orderby('name ASC')->all(), 'user_id', 'name');
?>

<div class="open-order-form">

    <?php $form = ActiveForm::begin(); ?>
	
		<div class='col-sm-12 col-md-6 col-lg-6'>
			<?= $form->field($model, 'lot_id')->dropDownList($lots, ['prompt'=>'Select lots...']); ?>
		</div>
		<div class='col-sm-12 col-md-6 col-lg-6'>
			<?= $form->field($model, 'user_id')->label('User')->dropDownList($users, ['prompt'=>'Select buyer...']); ?>
		</div>
		<div class='col-sm-12 col-md-6 col-lg-6'>
			<?= $form->field($model, 'number_of_box')->textInput() ?>
		</div>
		<div class='col-sm-12 col-md-6 col-lg-6'>
			<?= $form->field($model, 'total_weight')->textInput() ?>
		</div>
		<div class='col-sm-12 col-md-4 col-lg-4'>
			<?= $form->field($model, 'shipping_cost')->textInput() ?>
		</div>
		<div class='col-sm-12 col-md-4 col-lg-4'>
			<?= $form->field($model, 'shipping_cost_usd')->textInput() ?>
		</div>
		<div class='col-sm-12 col-md-4 col-lg-4'>
			<?= $form->field($model, 'additional_cost')->label("Additional Cost ($)")->textInput() ?>
		</div>
		
		<div class='col-sm-12 col-md-6 col-lg-6'>
			<?= $form->field($model, 'remark')->widget(CKEditor::className(), [
				'options' => ['rows' => 6],
				'preset' => 'basic'
			]) ?>
		</div>
		
		<div class='col-sm-12 col-md-6 col-lg-6'>
			<?= $form->field($model, 'shipping_explanation')->widget(CKEditor::className(), [
				'options' => ['rows' => 6],
				'preset' => 'basic'
			]) ?>
		</div>
		
		<div class="col-sm-12 form-group">
			<?= Html::submitButton('Save & Continue', ['class' => 'btn btn-success']) ?>
		</div>

    <?php ActiveForm::end(); ?>

</div>

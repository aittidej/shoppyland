<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

use app\models\DiscountList;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Lot */

$this->title = 'Create Lot';
$this->params['breadcrumbs'][] = ['label' => 'Lots', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$discountLists = ArrayHelper::map(DiscountList::find()->where(['status'=>'1'])->orderby('title ASC')->all(), 'discount_list_id', 'title');
$users = ArrayHelper::map(User::find()->where(['role_id'=>2, 'status'=>'1'])->orderby('name ASC')->all(), 'user_id', 'name');
?>
<style>
	table {
		font-family: arial, sans-serif;
		font-size: 11px;
		border-collapse: collapse;
		width: 100%;
	}

	td, th {
		border: 1px solid #dddddd;
		text-align: left;
		padding: 8px;
	}

	tr:nth-child(even) { background-color: #d2e6ea; }
</style>

<div class="lot-create">

    <h1><?= Html::encode($this->title) ?></h1>

	<?php $form = ActiveForm::begin(); ?>
		
		<div class='col-sm-12 col-md-6 col-lg-6'>
			<?= $form->field($model, 'lot_number')->textInput(['required'=>true]) ?>
			
			<?= $form->field($model, 'user_id')->dropDownList($users, ['prompt'=>'(All buyer)']); ?>

			<?= $form->field($model, 'discount_list_id')->label('Discount')->dropDownList($discountLists, ['prompt'=>'Select discount...']); ?>

			<?= $form->field($model, 'price')->label('Price ($)')->textInput() ?>
		</div>
		
		<div class='col-sm-12 col-md-6 col-lg-6'>
			<?= $form->field($model, 'items')->label('Barcode (UPC)')->textarea(['rows' => '20', 'id'=>'items-field']) ?>
		</div>

		<div class="clearfix"></div><br>
		
		<div class="form-group">
			<center><?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?></center>
		</div>

    <?php ActiveForm::end(); ?>

</div>

<?php $this->registerJs("

	$('.discount').change(function (e) {
		
		alert('test');
		
	});

",View::POS_READY); ?>
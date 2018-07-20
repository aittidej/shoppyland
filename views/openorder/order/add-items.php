<?php

use yii\web\View;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OpenOrder */

$user = $model->user;
$this->title = 'Add Items for '.$user->name.' - Lot #'.$model->lot_number;
$this->params['breadcrumbs'][] = ['label' => 'Open Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="open-order-create">

    <h1><?= Html::encode($this->title) ?></h1>

	<?php $form = ActiveForm::begin(); ?>

		<div class='col-sm-12 col-md-4 col-lg-4'>
			<?= $form->field($model, 'items')->label('Barcode (UPC)')->textarea(['rows' => '20']) ?>
		</div>
		
		<div class='col-sm-12 col-md-8 col-lg-8'>
			<span id="result"></span>
			<?php
				
			?>
		</div>

		<div class="form-group col-sm-12">
			<?= Html::submitButton('Add', ['class' => 'btn btn-success']) ?>
		</div>

	<?php ActiveForm::end(); ?>
	
</div>
<?php 
$this->registerJs("
	




", View::POS_READY);
?>
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\LotRelSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lot-rel-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class='col-sm-10'>
		<?= $form->field($model, 'upc')->label('UPC'); ?>
	</div>

    <div class="col-sm-2 form-group"><br>
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Reset', ['/lot-search'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

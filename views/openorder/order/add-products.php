<?php

use yii\web\View;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

use app\models\Brand;

/* @var $this yii\web\View */
/* @var $model app\models\OpenOrder */

$this->title = 'Add Products';
$this->params['breadcrumbs'][] = ['label' => 'Open Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


$brands = ArrayHelper::map(Brand::find()->where(['status'=>'1'])->orderby('title ASC')->all(), 'brand_id', 'title');
?>
<div class="open-order-create">

    <h1><?= Html::encode($this->title) ?></h1>

	<?php $form = ActiveForm::begin(); ?>

		<?php foreach ($products as $index => $product) { ?>
			<div class='col-sm-12 col-md-6 col-lg-2'>
				<?= $form->field($product, "[$index]upc")->textInput(['maxlength' => true, 'readonly'=>'readonly']) ?>
			</div>
			
			<div class='col-sm-12 col-md-6 col-lg-4'>
				<?= $form->field($product, "[$index]title")->textInput(['maxlength' => true]) ?>
			</div>
			
			<div class='col-sm-12 col-md-6 col-lg-2'>
				<?= $form->field($product, "[$index]model")->textInput(['maxlength' => true]) ?>
			</div>
			
			<div class='col-sm-12 col-md-6 col-lg-2'>
				<?= $form->field($product, "[$index]brand_id")->dropDownList($brands, ['prompt'=>'Select Brand...']); ?>
			</div>
			
			<div class='col-sm-12 col-md-6 col-lg-2'>
				<?= $form->field($product, "[$index]image_path")->textInput() ?>
			</div>
		<?php } ?>
		
    <div class="form-group col-sm-12">
        <?= Html::submitButton('Add', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
	
</div>
<?php 
$this->registerJs("
	




", View::POS_READY);
?>
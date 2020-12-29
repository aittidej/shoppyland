<?php

use yii\web\View;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\file\FileInput;

use app\models\Brand;

/* @var $this yii\web\View */
/* @var $model app\models\OpenOrder */

$this->title = 'Add Products';
if(empty($id))
	$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
else
	$this->params['breadcrumbs'][] = ['label' => 'Open Orders', 'url' => ['/openorder/order/view', 'id'=>$id]];
$this->params['breadcrumbs'][] = $this->title;

$brands = ArrayHelper::map(Brand::find()->where(['status'=>'1'])->orderby('title ASC')->all(), 'brand_id', 'title');
?>
<div class="open-order-create">

    <h1><?= Html::encode($this->title) ?></h1>

	<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data'], 'method' => 'POST']); ?>

		<?php foreach ($products as $index => $product) { ?>
			<div class='col-sm-12 col-md-12 col-lg-12'>
				<div class='col-sm-12 col-md-6 col-lg-2'>
					<?php
						$qty = empty($productList[$product->product_id]) ? '' : ' x '.$productList[$product->product_id];
						echo $form->field($product, "[$index]upc")->label('UPC'.$qty)->textInput(['maxlength' => true, 'readonly'=>'readonly']); 
					?>
				</div>
				
				<div class='col-sm-12 col-md-6 col-lg-6'>
					<?= $form->field($product, "[$index]title")->textInput(['maxlength' => true]) ?>
				</div>
				
				<div class='col-sm-12 col-md-6 col-lg-4'>
					<?= $form->field($product, "[$index]model")->textInput(['maxlength' => true]) ?>
				</div>
				
				<div class='col-sm-12 col-md-6 col-lg-3'>
					<?= $form->field($product, "[$index]brand_id")->dropDownList($brands, ['prompt'=>'Select Brand...']); ?>
				</div>
				
				<div class='col-sm-12 col-md-6 col-lg-3'>
					<?= $form->field($uploads[$index], "[$index]image[]")->fileInput(['multiple' => true]); ?>
				</div>
				
				<div class='col-sm-12 col-md-6 col-lg-3'>
					<?= $form->field($attachments[$index], "[$index]attachment[]")->label('Tag (5MB Max)')->fileInput(['multiple' => false]); ?>
				</div>
				
				<div class='col-sm-12 col-md-6 col-lg-3'><br>
					<?= Html::a('Coach Scan Tools', 'https://scan.coach.com/product/'.$product->upc, [ 'title' => 'Coach System', 'target'=>'_blank' ]); ?>
				</div>
			</div>
			<div class='col-sm-12 col-md-12 col-lg-12'><hr></div>
		<?php } ?>
		
		<div class="form-group col-sm-12">
			<div class="col-sm-12">
				<?= Html::submitButton('Add', ['class' => 'btn btn-success']) ?>
			</div>
		</div>

    <?php ActiveForm::end(); ?>
	
</div>
<?php 
$this->registerJs("
	




", View::POS_READY);
?>
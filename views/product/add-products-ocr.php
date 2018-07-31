<?php

use yii\web\View;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\file\FileInput;

use app\models\Brand;

/* @var $this yii\web\View */
/* @var $model app\models\OpenOrder */

$this->title = 'OCR - UPC #'.$product->upc ;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$brands = ArrayHelper::map(Brand::find()->where(['status'=>1])->orderby('title ASC')->all(), 'title', 'title');
?>
<div class="open-order-create">

    <h1><?= Html::encode($this->title) ?></h1>

	<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data'], 'method' => 'POST']); ?>
		
		<div class='col-sm-12'>
			<?= $form->field($upload, "attachment")->label('Price Tag')->fileInput(['multiple' => false]); ?>
		</div>
		
		<div class='col-sm-12'>
			<?php echo $form->field($image, "image[]")->fileInput(['multiple' => true]); ?>
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

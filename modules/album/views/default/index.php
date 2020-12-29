<?php
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<?php $form = ActiveForm::begin(['options' => ['id'=>'upload-form', 'enctype' => 'multipart/form-data']]) ?>

	<?= $form->field($upload, 'image[]')->fileInput(['multiple' => true, 'accept' => 'image/*']) ?>
	<button type="button" class="btn btn-success subm">Upload</button>
	
<?php ActiveForm::end(); ?>

<?php
$this->registerJs("
	
	$(document).ready(function () {
		
		$('.subm').click(function(e){
			var formData = new FormData($('#upload-form')[0]);
			console.log(formData);
			$.ajax({
				url: '".Yii::$app->getUrlManager()->createUrl('album/default/upload')."',  //Server script to process data
				type: 'POST',

				// Form data
				data: formData,

				//beforeSend: beforeSendHandler, // its a function which you have to define

				success: function(response) {
					console.log(response);
				},

				error: function(){
					alert('ERROR at PHP side!!');
				},


				//Options to tell jQuery not to process data or worry about content-type.
				cache: false,
				contentType: false,
				processData: false
			});
		});
	
	});
	
");
?>
<?php 
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<div class="form-gap"></div>
<div class="container">
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default">
              <div class="panel-body">
                <div class="text-center">
                  <h3><i class="fa fa-repeat fa-4x"></i></h3>
                  <h2 class="text-center">Reset Password</h2>
				  <?php
					if($error)
						echo "<p style='color:red;'>".$error."<p>";
					else
						echo "<p>You can reset your password here.</p>";
				  ?>
                  
                  <div class="panel-body">
    
	
					<?php $form = ActiveForm::begin([]); ?>


						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon"><i class="glyphicon glyphicon-lock color-blue"></i></span>
								<input id="password" name="password" placeholder="password" class="form-control"  type="password">
							</div><br>
							<div class="input-group">
								<span class="input-group-addon"><i class="glyphicon glyphicon-lock color-blue"></i></span>
								<input id="confirm_password" name="confirm_password" placeholder="confirm password" class="form-control"  type="password">
							</div>
						</div>
						<div class="form-group">
							<input name="recover-submit" class="btn btn-lg btn-primary btn-block" value="Reset Password" type="submit">
						</div>

						<input type="hidden" class="hide" name="token" id="token" value="<?= $token ?>"> 

					<?php ActiveForm::end(); ?>
    
                  </div>
                </div>
              </div>
            </div>
          </div>
	</div>
</div>

















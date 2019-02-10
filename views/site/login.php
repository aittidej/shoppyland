<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
//use yii\bootstrap\ActiveForm;
//use kartik\form\ActiveField;

use kartik\widgets\ActiveForm;
use kartik\widgets\ActiveField;

$this->title = 'Login';
//$this->params['breadcrumbs'][] = $this->title;
?>
<style>
body {
    background-color: white;
}

#loginbox {
    margin-top: 30px;
}

#loginbox > div:first-child {        
    padding-bottom: 10px;    
}

.iconmelon {
    display: block;
    margin: auto;
}

#form > div {
    margin-bottom: 25px;
}

#form > div:last-child {
    margin-top: 10px;
    margin-bottom: 10px;
}

.panel {    
    background-color: transparent;
}

.panel-body {
    padding-top: 30px;
    background-color: rgba(2555,255,255,.3);
}

#particles {
    width: 100%;
    height: 100%;
    overflow: hidden;
    top: 0;                        
    bottom: 0;
    left: 0;
    right: 0;
    position: absolute;
    z-index: -2;
}

.iconmelon,
.im {
  position: relative;
  width: 150px;
  height: 150px;
  display: block;
  fill: #525151;
}

.iconmelon:after,
.im:after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}
</style>
<div class="container">    
        
    <div id="loginbox" class="mainbox col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3"> 
        
        <div class="row">                
            <a href="/" class="iconmelon">
				<img src="/images/new-logo.png" height="150px">
            </a>
        </div>
        
        <div class="panel panel-default" >
            <div class="panel-heading">
                <div class="panel-title text-center">
					<?= Html::a('Shoppyland by Honey', ['/']) ?>
				</div>
            </div>

            <div class="panel-body" >
                <?php $form = ActiveForm::begin([
					'id' => 'form',
					'class' => 'form-horizontal',
					//'layout' => 'horizontal',
					/*'fieldConfig' => [
						'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
						'labelOptions' => ['class' => 'col-lg-1 control-label'],
					],*/
				]); ?>
					
					<div class="input-group col-sm-12">
                        <?= $form->field($model, 'username', [
							'addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-user"></i>']]
						])->label(false)->textInput(['class'=>'form-control', 'style'=>'width: 100%; height: 45px;', 'placeholder'=>'Username', 'autofocus' => true]); ?>
					</div>

                    <div class="input-group col-sm-12">
                        <?= $form->field($model, 'password', [
							'addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-lock"></i>']]
							])->label(false)->passwordInput(['class'=>'form-control', 'style'=>'width: 100%; height: 45px;', 'placeholder'=>'Password']); ?>
					</div>
					
					<div class="input-group">
						<?= $form->field($model, 'rememberMe')->checkbox([]) ?>
					</div>
					
                    <div class="form-group">
                        <!-- Button -->
                        <div class="col-sm-12 controls">
                            <?= Html::submitButton('<i class="glyphicon glyphicon-log-in"></i> Log in', ['class' => 'btn btn-primary pull-right', 'name' => 'login-button']) ?>
                        </div>
                    </div>

                <?php ActiveForm::end(); ?>

            </div>                     
        </div>  
    </div>
</div>
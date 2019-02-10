<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */

?>
<!--     Fonts and icons     -->
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
<link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
<!-- CSS Files -->
<link href="/css/now-ui-dashboard.css?v=1.0.1" rel="stylesheet" />
<!-- CSS Just for demo purpose, don't include it in your project -->
<!--<link href="/css/demo.css" rel="stylesheet" />-->

<div class="row" style="margin-top: 55px;">
	<div class="col-md-4 col-md-push-8">
		<div class="card card-user">
			<div class="image">
				<img src="/images/website/cloud.jpg" alt="...">
			</div>
			<div class="card-body">
				<div class="author">
					<a href="#">
						<img class="avatar border-gray" src="/images/website/default-female.jpg" alt="Default">
						<h5 class="title"><?= $user->name ?></h5>
					</a>
					<p class="description">
						<?= $user->email ?>
					</p>
				</div>
				<!--<p class="description text-center">
					"Lamborghini Mercy
					<br> Your chick she so thirsty
					<br> I'm in that two seat Lambo"
				</p>-->
			</div>
			<!--<hr>
			<div class="button-container">
				<button href="#" class="btn btn-neutral btn-icon btn-round btn-lg">
					<i class="fab fa-facebook-f"></i>
				</button>
				<button href="#" class="btn btn-neutral btn-icon btn-round btn-lg">
					<i class="fab fa-twitter"></i>
				</button>
				<button href="#" class="btn btn-neutral btn-icon btn-round btn-lg">
					<i class="fab fa-google-plus-g"></i>
				</button>
			</div>-->
		</div>
	</div>
	<div class="col-md-8 col-md-pull-4">
		<div class="card">
			<div class="card-header">
				<h5 class="title">Edit Profile</h5>
			</div>
			<div class="card-body">
				<?php $form = ActiveForm::begin(); ?>
					<!--<div class="row">
						<div class="col-md-5 pr-1">
							<div class="form-group">
								<label>Company (disabled)</label>
								<input type="text" class="form-control" disabled="" placeholder="Company" value="Creative Code Inc.">
							</div>
						</div>
						<div class="col-md-3 px-1">
							<div class="form-group">
								<label>Username</label>
								<input type="text" class="form-control" placeholder="Username" value="michael23">
							</div>
						</div>
						<div class="col-md-4 pl-1">
							<div class="form-group">
								<label for="exampleInputEmail1">Email address</label>
								<input type="email" class="form-control" placeholder="Email">
							</div>
						</div>
					</div>-->
					<div class="row">
						<div class="col-md-6 pr-1">
							<div class="form-group">
								<?= $form->field($user, 'username')->textInput(['class' => 'form-control', 'placeholder'=>'Username']) ?>
							</div>
						</div>
						<div class="col-md-6 pl-1">
							<div class="form-group">
								<?= $form->field($user, 'email')->textInput(['class' => 'form-control', 'placeholder'=>'Email']) ?>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 pr-1">
							<div class="form-group">
								<?= $form->field($user, 'name')->textInput(['class' => 'form-control', 'placeholder'=>'Name']) ?>
							</div>
						</div>
						<div class="col-md-6 pl-1">
							<div class="form-group">
								<?= $form->field($user, 'phone')->textInput(['class' => 'form-control', 'placeholder'=>'Phone']) ?>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<?= $form->field($user, 'address')->label('Address')->textarea(['rows' => 6, 'cols'=> 80]); ?>
								
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-4 col-md-offset-4">
							<?= Html::submitButton('Save', ['class' => 'btn btn-primary btn-block', 'style'=>'background-color: #f96332; border-color: #f96332;']) ?>
						</div>
					</div>
				<?php ActiveForm::end(); ?>
			</div>
		</div>
	</div>
</div>

<!--   Core JS Files   -->
<!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->
<script src="/js/now-ui-dashboard.js?v=1.0.1"></script>
<!-- Now Ui Dashboard DEMO methods, don't include it in your project! -->
<script src="/js/demo.js"></script>
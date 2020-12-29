<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\WebsiteAsset;
use yii\helpers\Url;
use yii\helpers\Html;

WebsiteAsset::register($this);
?>
<!--================Offcanvus Menu Area =================-->
<div class="side_menu">
	<?= Html::a('<img src="/images/new-logo.png" width="150px" alt="">', ['/'], ['class'=>'logo', 'width'=>'150px']) ?>
	<ul class="list menu_right">
		<li>
			<?= Html::a('Home', ['/'], []) ?>
		</li>
		<li>
			<?= Html::a('Our Rate', ['/website/rate'], []) ?>
		</li>
		<li>
			<?= Html::a('Payment Methods', ['/website/payment-methods?r=peung'], []) ?>
		</li>
		<li>
			<?= Html::a('Contact Us', ['/#question-form'], []) ?>
		</li>
		<?php if (!Yii::$app->user->isGuest) { ?>
			<li>
				<?= Html::a('Dashboard', ['/site/dashboard']); ?>
			</li>
		<?php } ?>
		<!--<li>
			<a href="book-table.html">Book a table</a>
		</li>
		<li>
			<a href="#">Pages</a>
			<ul class="list">
				<li>
					<a href="gallery.html">Gallery</a>
				</li>
				<li>
					<a href="elements.html">Elements</a>
				</li>
			</ul>
		</li>
		<li>
			<a href="#">Blog</a>
			<ul class="list">
				<li>
					<a href="blog.html">Blog</a>
				</li>
				<li>
					<a href="single-blog.html">Blog Details</a>
				</li>
			</ul>
		</li>-->
		<li>
			<?php
			if (Yii::$app->user->isGuest) 
			{
				echo Html::a('Login', ['/site/login']);
			}
			else
			{
				echo Html::beginForm(['/site/logout'], 'post')
					. Html::submitButton(
						'Logout (' . Yii::$app->user->identity->name . ')',
						['class' => 'btn btn-link logout', 'style' => '']
					)
					. Html::endForm();
			}
			?>
		</li>
	</ul>
	
	<ul class="list social">
		<li>
			<a href="https://www.facebook.com/yuwatida85" target="_blank">
				<i class="fa fa-facebook"></i>
			</a>
		</li>
		<li>
			<a href="https://www.instagram.com/yuwatidahoney/" target="_blank">
				<i class="fa fa-instagram"></i>
			</a>
		</li>
		<li>
			<a href="https://line.me/ti/p/VyICG_e9u8" target="_blank">
				<span style='font-size: 10.5px;'>LINE</span> 
			</a>
		</li>
	</ul>
</div>
<!--================End Offcanvus Menu Area =================-->

<!--================Header Menu Area =================-->
<header class="header_area home_menu">
	<div class="main_menu">
		<nav class="navbar navbar-expand-lg navbar-light">
			<div class="container">
				<!-- Brand and toggle get grouped for better mobile display -->
				<?= Html::a('<img src="/images/new-logo.png" width="75px" alt="">', ['/'], ['class'=>'navbar-brand logo_h']) ?>
				<button class="navbar-toggler" id="small-hamburger" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
				 aria-expanded="false" aria-label="Toggle navigation">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="navbar-collapse offset collapse" id="navbarSupportedContent" style='overflow: hidden;'>
				<!-- navbar-collapse offset collapse show -->
					<ul class="nav navbar-nav menu_nav ml-auto">
						<li class="nav-item active">
							<a class="nav-link" href="/">Home</a>
						</li>
						<li class="nav-item">
							<?= Html::a('Our Rate', ['/website/rate'], ['class'=>'nav-link']) ?>
						</li>
						<li class="nav-item">
							<?= Html::a('Payment Methods', ['/website/payment-methods?r=peung'], ['class'=>'nav-link']) ?>
						</li>
						<li class="nav-item">
							<?= Html::a('Contact Us', ['/#question-form'], ['class'=>'nav-link']) ?>
						</li>
						<?php if (!Yii::$app->user->isGuest) { ?>
							<li class="nav-item">
								<?= Html::a('Dashboard', ['/site/dashboard'], ['class'=>'nav-link']); ?>
							</li>
						<?php } ?>
						<li class="nav-item">
							<?php
							if (Yii::$app->user->isGuest) 
							{
								echo Html::a('Login', ['/site/login'], ['class'=>'nav-link']);
							}
							else
							{
								echo Html::beginForm(['/site/logout'], 'post')
									. Html::submitButton(
										'Logout (' . Yii::$app->user->identity->name . ')',
										['class' => 'nav-link btn btn-link logout', 'style' => '']
									)
									. Html::endForm();
							}
							?>
						</li>
					</ul>
				</div>
			</div>
		</nav>
	</div>
</header>
<!--================Header Menu Area =================-->

<!--================Canvus Menu Area =================-->
<div class="canvus_menu">
	<div class="container">
		<div class="float-right">
			<div class="toggle_icon">
				<span></span>
			</div>
		</div>
	</div>
</div>
<!--================End Canvus Menu Area =================-->
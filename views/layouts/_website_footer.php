<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\WebsiteAsset;
use yii\helpers\Url;
use yii\helpers\Html;

WebsiteAsset::register($this);
?>

<!--================ start footer Area  =================-->
<footer class="footer-area" style="padding-top: 50px;padding-bottom: 50px;">
	<div class="container">
		<div class="row">
			<div class="col-lg-2  col-md-6 col-sm-6">
				<div class="single-footer-widget tp_widgets">
					<h6 class="footer_title">Company</h6>
					<ul class="list">
						<li>
							<?= Html::a('About Us', ['/website/about']) ?>
						</li>
						<li>
							<?php //Html::a('Brands', ['/website/brand'], []) ?>
							<?= Html::a('Referrals Program', ['/website/referrals-program'], []) ?>
						</li>
						<li>
							<?= Html::a('Become our Partner', ['/website/partnership'], []) ?>
						</li>
					</ul>
				</div>
			</div>
			<div class="col-lg-2  col-md-6 col-sm-6">
				<div class="single-footer-widget tp_widgets">
					<h6 class="footer_title">Information</h6>
					<ul class="list">
						<li>
							<?= Html::a('Our Rate', ['/website/rate'], []) ?>
						</li>
						<li>
							<?= Html::a('Payment Methods', ['/website/payment-methods?r=peung'], []) ?>
						</li>
						<li>
							<?= Html::a('Shipping Information', ['/website/shipping-information'], []) ?>
						</li>
					</ul>
				</div>
			</div>
			<div class="col-lg-2  col-md-6 col-sm-6">
				<div class="single-footer-widget tp_widgets">
					<h6 class="footer_title">Support</h6>
					<ul class="list">
						<li>
							<?= Html::a('Contact Us', ['/#question-form'], []) ?>
						</li>
						<li>
							<?= Html::a('Order Status', ['/website/order-status'], []) ?>
						</li>
						<li>
							<?= Html::a('Returns & Exchanges', ['/website/shipping-information'], []) ?>
						</li>
					</ul>
				</div>
			</div>
			<div class="col-lg-2  col-md-6 col-sm-6">
				<!--<div class="single-footer-widget tp_widgets">
					<h6 class="footer_title">Resources</h6>
					<ul class="list">
						<li>
							<a href="#">Guides</a>
						</li>
						<li>
							<a href="#">Research</a>
						</li>
						<li>
							<a href="#">Experts</a>
						</li>
						<li>
							<a href="#">Agencies</a>
						</li>
					</ul>
				</div>-->
			</div>
			<div class="col-lg-4 col-md-6 col-sm-6">
				<aside class="f_widget news_widget">
					<div class="f_title">
						<h3 class="footer_title">Get Updates</h3>
					</div>
					<p>Send me tips, trends, freebies, updates & offers.</p>
					<div id="mc_embed_signup">
						<form action="https://shoppylandbyhoney.us20.list-manage.com/subscribe/post?u=e844698099f74bac2d1393032&amp;id=d39018df3a" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate subscribe_form relative" target="_blank" novalidate>
							<div class="input-group d-flex flex-row">
								<input name="EMAIL" placeholder="Enter email address" id="mce-EMAIL" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Email Address '" required="" type="email">
								<button type="submit" class="btn sub-btn">
									<span class="lnr lnr-arrow-right"></span>
								</button>
							</div>
							<div class="mt-10 info" style="background: #04091E;"></div>
						</form>
					</div>
				</aside>
			</div>
		</div>
		<div class="row footer-bottom d-flex justify-content-between align-items-center">
			<p class="col-lg-8 col-md-8 footer-text m-0">
				<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
				Copyright&copy; <?= date('Y') ?> All rights reserved | Shoppyland by Honey 
				<a href="https://colorlib.com" style="font-size: 4px; color: #04091E;" target="_blank">Colorlib</a>
				<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
			</p>
			<div class="col-lg-4 col-md-4 footer-social">
				<a href="https://www.facebook.com/yuwatida85" target="_blank">
					<i class="fa fa-facebook"></i>
				</a>
				<a href="https://www.instagram.com/yuwatidahoney/" target="_blank">
					<i class="fa fa-instagram"></i>
				</a>
				<a href="https://line.me/ti/p/VyICG_e9u8" target="_blank">
					<span style='font-size: 10.5px;'>LINE</span> 
				</a>
			</div>
		</div>
	</div>
</footer>
<!--================ End footer Area  =================-->
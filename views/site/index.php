<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */

$this->title = Yii::$app->name;

$websiteAsset = \app\assets\WebsiteAsset::register($this);
$baseUrl = $websiteAsset->baseUrl;
?>
<style>
.canvus_menu .toggle_icon span:before { background-color: white; }
.canvus_menu .toggle_icon span { background-color: white; }
.canvus_menu .toggle_icon span:after { background-color: white; }
#myVideo {
	position: absolute;
	object-fit: cover;
    min-height: 300px;
    height: 100vh;
	width: 100%;
	background-color: black;
    max-height: 100vh;
    opacity: 1;
    transition: all 0.75s cubic-bezier(0.2, 0.3, 0.25, 0.9) 0s;
}
.text-header { color: white; }
.text-header h1 { font-size: 24px; }
@media only screen and (max-width: 960px) {
    #video_bg { display:none; }
    #myVideo { display:none; }
	.text-header { color: black; }
}
</style>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/1.6.11/css/lightgallery.min.css">
	
	<div id="video_bg">
		<video autoplay muted loop id="myVideo">
			<source src="/video/video_bg.mp4" type="video/mp4">
			Your browser does not support HTML5 video.
		</video>
	</div>
	<!--================ Start banner section =================-->
	<section class="home_banner relative">
		<div class="container-fluid pl-0">
			<div class="row justify-content-center align-items-center full_height">
				<div class="col-lg-6 p-0">
					<div class="banner_left d-flex justify-content-center flex-column">
						<h1 class="text-header">shoppyland by honey</h1>
						<p class="text-header">รับพรีสินค้าทุกชนิดจากอเมริกา ทั้งปลีก/ส่ง ซื้อเองขายเอง แม่ค้าอยู่อเมริกาจ้า รับกดจากเว็ปด้วย</p>
						<?= Html::a('Get Started', ['/website/partnership'], ['class'=>'main_btn']) ?>
					</div>
				</div>
				<div class="col-lg-6">
					<!--<div class="banner_right d-flex justify-content-center align-items-center">
						<div class="round-planet planet">
							<div class="round-planet planet2">
								<div class="round-planet planet3">
									<div class="shape shape1" style="border-radius: 0;top: -33%;"></div>
									<div class="shape shape2" style="border-radius: 0;left: 100%;"></div>
									<div class="shape shape3" style="border-radius: 0;left: 134%;"></div>
									<div class="shape shape4" style="border-radius: 0;left: 98%;"></div>
									<div class="shape shape5" style="border-radius: 0;top: -2%;"></div>
									<div class="shape shape6" style="border-radius: 0;bottom: -61%;"></div>
								</div>
							</div>
						</div>
					</div>-->
				</div>
			</div>
		</div>
		<!--<img class="face-img img-fluid" src="/website/images/mini-bennett.png" alt="Coach Mini Bennett">-->
	</section>
	<!--================ End banner section =================-->

	<!--================ Top Dish Area =================-->
	<section class="top_dish_area">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class="main_title position-relative">
						<h1>Our Top Rated Items</h1>
						<hr>
						<div class="round-planet planet">
							<div class="round-planet planet2">
								<div class="shape shape1" style="border-radius: 0;"></div>
								<div class="shape shape2" style="border-radius: 0;"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<?php foreach($hightlightProducts AS $hightlightProduct) { ?>
					<div class="single_dish col-lg-4 col-md-6 text-center">
						<div class="thumb">
							<?= Html::img($hightlightProduct->firstImage, ['class'=>'img-fluid', 'alt'=>$hightlightProduct->title]) ?>
						</div>
						<h4><?= $hightlightProduct->title; ?></h4>
						<p><?= $hightlightProduct->model; ?></p>
						<h5 class="price">$<?= empty($hightlightProduct->base_price) ? '0.00' : number_format($hightlightProduct->base_price, 2); ?></h5>
					</div>
				<?php } ?>
			</div>
		</div>
	</section>
	<!--================ End Top Dish Area =================-->

	<!--================ Menu Area =================-->
	<section class="menu_area">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class="main_title position-relative">
						<h1>Our favourite Item</h1>
						<hr>
						<div class="round-planet planet">
							<div class="round-planet planet2">
								<div class="shape shape1" style="border-radius: 0;"></div>
								<div class="shape shape2" style="border-radius: 0;"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row menu_inner">
				<div class="col-md-12 col-lg-10 col-xl-6">
					<div class="menu_list">
						<ul class="list">
							<?php
								$firstHalf = (count($sellingLists) % 2 == 0) ? (count($sellingLists)/2)-1 : floor(count($sellingLists)/2);
								for($i=0; $i <= $firstHalf; $i++)
								{
									$sellingList = $sellingLists[$i];
									$title = $sellingList['product']->title;
									$title = substr($title, 0, 28);
									echo "<li>
											<h4>".$title."
												<span>$".number_format($sellingList->price, 2)."</span>
											</h4>
											<p>".$sellingList['product']->model."</p>
										</li>";
								}
							?>
						</ul>
					</div>
				</div>

				<div class="col-md-12 col-lg-10 col-xl-6">
					<div class="menu_list pr-0">
						<ul class="list">
							<?php
								for($j=ceil(count($sellingLists)/2); $j < count($sellingLists); $j++)
								{
									$sellingList = $sellingLists[$j];
									$title = $sellingList['product']->title;
									$title = substr($title, 0, 30);
									echo "<li>
											<h4>".$title."
												<span>$".number_format($sellingList->price, 2)."</span>
											</h4>
											<p>".$sellingList['product']->model."</p>
										</li>";
								}
							?>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--================End Menu Area =================-->

	<!--================ Gallery Area =================-->
	<section class="gallery_area" style="padding: 100px 0 0;">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class="main_title position-relative">
						<h1>fashion galleries</h1>
						<hr>
						<div class="round-planet planet">
							<div class="round-planet planet2">
								<div class="shape shape1" style="border-radius: 0;"></div>
								<div class="shape shape2" style="border-radius: 0;"></div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row" id="lightgallery">
			
			
				<div class="col-lg-4 all-image" data-src="/images/website/g1.jpg">
					<div class="single-gallery">
						<div class="overlay"></div>
						<img class="img-fluid w-100" src="/images/website/g1.jpg" alt="">
						<div class="content">
							<i class="lnr lnr-picture"></i>
						</div>
					</div>
				</div>
				
				<div class="col-lg-4 all-image" data-src="/images/website/g6.jpg">
					<div class="single-gallery">
						<div class="overlay"></div>
						<img class="img-fluid w-100" src="/images/website/g6.jpg" alt="">
						<div class="content">
							<i class="lnr lnr-picture"></i>
						</div>
					</div>
				</div>
				
				<div class="col-lg-4 all-image" data-src="/images/website/g4.jpg">
					<div class="single-gallery">
						<div class="overlay"></div>
						<img class="img-fluid w-100" src="/images/website/g4.jpg" alt="">
						<div class="content">
							<i class="lnr lnr-picture"></i>
						</div>
					</div>
				</div>
				
				<div class="col-lg-4 all-image" data-src="/images/website/g3.jpg">
					<div class="single-gallery">
						<div class="overlay"></div>
						<img class="img-fluid w-100" src="/images/website/g3.jpg" alt="">
						<div class="content">
							<i class="lnr lnr-picture"></i>
						</div>
					</div>
				</div>
				
				<div class="col-lg-4 all-image" data-src="/images/website/g5.jpg">
					<div class="single-gallery">
						<div class="overlay"></div>
						<img class="img-fluid w-100" src="/images/website/g5.jpg" alt="">
						<div class="content">
							<i class="lnr lnr-picture"></i>
						</div>
					</div>
				</div>

				<div class="col-lg-4 all-image" data-src="/images/website/g2.jpg">
					<div class="single-gallery">
						<div class="overlay"></div>
						<img class="img-fluid w-100" src="/images/website/g2.jpg" alt="">
						<div class="content">
							<i class="lnr lnr-picture"></i>
						</div>
					</div>
				</div>
				<!--<div class="col-lg-4 all-image" data-src="/images/website/g7.jpg">
					<div class="single-gallery">
						<div class="overlay"></div>
						<img class="img-fluid w-100" src="/images/website/g7.jpg" alt="">
						<div class="content">
							<i class="lnr lnr-picture"></i>
						</div>
					</div>
				</div>-->
				
				
			</div>
		</div>
	</section>
	<!--================ End Gallery Area =================-->

	<!--================Member Area =================-->
	<!--<section class="testimonials_area section_gap position-relative">
		<div class="container">
			<div class="testi_slider owl-carousel">
				<div class="item">
					<div class="row">
						<div class="col-lg-4">
							<img src="<?= $baseUrl; ?>/img/testimonials/testi-1.jpg" alt="">
						</div>
						<div class="col-lg-8">
							<div class="testi_text">
								<h4>Filipino Gomez</h4>
								<h5>Web Developer, Google</h5>
								<p>“Who are in extremely love with eco friendly system. Lorem ipsum dolor sit amet, consectetur adipisicing elit,
									sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation
									ullamco laboris nisi ut aliquip ex ea commodo consequat.”</p>
							</div>
						</div>
					</div>
				</div>
				<div class="item">
					<div class="row">
						<div class="col-lg-4">
							<img src="<?= $baseUrl; ?>/img/testimonials/testi-1.jpg" alt="">
						</div>
						<div class="col-lg-8">
							<div class="testi_text">
								<h4>Filipino Gomez</h4>
								<h5>Web Developer, Google</h5>
								<p>“Who are in extremely love with eco friendly system. Lorem ipsum dolor sit amet, consectetur adipisicing elit,
									sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation
									ullamco laboris nisi ut aliquip ex ea commodo consequat.”</p>
							</div>
						</div>
					</div>
				</div>
				<div class="item">
					<div class="row">
						<div class="col-lg-4">
							<img src="<?= $baseUrl; ?>/img/testimonials/testi-1.jpg" alt="">
						</div>
						<div class="col-lg-8">
							<div class="testi_text">
								<h4>Filipino Gomez</h4>
								<h5>Web Developer, Google</h5>
								<p>“Who are in extremely love with eco friendly system. Lorem ipsum dolor sit amet, consectetur adipisicing elit,
									sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation
									ullamco laboris nisi ut aliquip ex ea commodo consequat.”</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>-->
	<!--================End Member Area =================-->

	<!--================Book Table Area =================-->
	<section class="reservation">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class="main_title position-relative">
						<h1 id="question-form">Have a Question?</h1>
						<hr>
						<div class="round-planet planet">
							<div class="round-planet planet2">
								<div class="shape shape1" style="border-radius: 0;"></div>
								<div class="shape shape2" style="border-radius: 0;"></div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="book_table_inner row align-items-center">
				<div class="offset-lg-2 offset-md-1 col-lg-8 col-md-10">
					<div class="table_form">
						<!---->
						<?php $form = ActiveForm::begin(['method' => 'POST', 'class' => 'book_table_form row']); ?>
							<div class="form-group col-md-12">
								<input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
							</div>
							<div class="form-group col-md-12">
								<input type="email" class="form-control" id="email" name="email" placeholder="Enter email address" required>
							</div>
							<div class="form-group col-md-12">
								<input type="tel" class="form-control" id="phone" name="phone" placeholder="Phone Number (optional)">
							</div>
							<div class="form-group col-md-12">
								<?= Html::textarea('message', NULL, ['class' => 'form-control', 'id'=>'message', 'placeholder' => 'Message', 'rows'=>6, 'required'=>true]);  ?>
							</div>
							<div class="form-group col-md-12">
								<center><?= Html::submitButton('Submit', ['class' => 'btn submit_btn form-control']); ?></center>
							</div>
						<?php ActiveForm::end(); ?>
						
						<center>
							<h3>or</h3>
							<h2>(858) 276-0603</h2>
						</center>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--================End Book Table Area =================-->

	
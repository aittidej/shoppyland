<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Brands';
?>
<style>
.ribbon {
    position: absolute;
    right: -5px;
    top: -5px;
    z-index: 1;
    overflow: hidden;
    width: 75px;
    height: 75px;
    text-align: center;
}

.ribbon span {
	font-size: 10px;
	color: #fff;
	text-transform: uppercase;
	text-align: center;
	font-weight: bold;
	line-height: 20px;
	transform: rotate(45deg);
	width: 100px;
	display: block;
	background: #F62459;
	background: linear-gradient(#ff89b1 0%, #ff006e 100%);
	box-shadow: 0 3px 10px -5px rgba(0, 0, 0, 1);
	position: absolute;
	top: 19px;
	right: -21px;

}
.ribbon span::before {
	content: '';
	position: absolute;
	left: 0%;
	top: 100%;
	z-index: -1;
	border-left: 3px solid #F62459;
	border-right: 3px solid transparent;
	border-bottom: 3px solid transparent;
	border-top: 3px solid #F62459;
}

.ribbon span::after {
	content: '';
	position: absolute;
	right: 0%;
	top: 100%;
	z-index: -1;
	border-right: 3px solid #F62459;
	border-left: 3px solid transparent;
	border-bottom: 3px solid transparent;
	border-top: 3px solid #F62459;
}

#inventory .card:hover {
    transition: all 0.3s ease;
    box-shadow: 12px 15px 20px 0px rgba(46,61,73,0.15);
}
#inventory .view-btn {
    background-color: #e6de08;
    margin: -25px 0 0 0;
    border-radius: 0 0 0 0;
    font-size: 14px;
    border: #e6de08;
    color: #000;

}
#inventory .btn:hover {
	background-color: #ff4444;
	color: #fff;
	border: 2px solid #ff4444;
	transition: all 0.3s ease;
	box-shadow: 12px 15px 20px 0px rgba(46,61,73,0.15);
}
.brand-grid { padding-bottom: 25px; }
</style>

<section class="reservation">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="main_title position-relative">
					<h1><?= Html::encode($this->title) ?></h1>
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
			<div class="col-md-3 brand-grid">
				<div class="card">
					<img class="img-fluid" src="/images/website/brands/12847.png" alt="ADIDAS">
					<button class="btn view-btn">Adidas</button>
				</div>
			</div>
			<div class="col-md-3 brand-grid">
				<div class="card">
					<img class="img-fluid" src="/images/website/brands/13629.png" alt="Calvin Kleins">
					<button class="btn view-btn">Calvin Kleins</button>
				</div>
			</div>
			<div class="col-md-3 brand-grid">
				<div class="card">
					<img class="img-fluid" src="/images/website/brands/17524.png" alt="Coach">
					<button class="btn view-btn">Coach</button>
					<div class="ribbon"><span>Deal</span></div>
				</div>
			</div>
			<div class="col-md-3 brand-grid">
				<div class="card">
					<img class="img-fluid" src="/images/website/brands/12951.png" alt="Disney">
					<button class="btn view-btn">Disney</button>
				</div>
			</div>
			<div class="col-md-3 brand-grid">
				<div class="card">
					<img class="img-fluid" src="/images/website/brands/13000.png" alt="Guess">
					<button class="btn view-btn">Guess</button>
				</div>
			</div>
			<div class="col-md-3 brand-grid">
				<div class="card">
					<img class="img-fluid" src="/images/website/brands/5667.png" alt="Kate Spade">
					<button class="btn view-btn">Kate Spade</button>
					<div class="ribbon"><span>Deal</span></div>
				</div>
			</div>
			<div class="col-md-3 brand-grid">
				<div class="card">
					<img class="img-fluid" src="/images/website/brands/9755.png" alt="Kipling">
					<button class="btn view-btn">Kipling</button>
				</div>
			</div>
			<div class="col-md-3 brand-grid">
				<div class="card">
					<img class="img-fluid" src="/images/website/brands/23308.png" alt="Michael Kors">
					<button class="btn view-btn">Michael Kors</button>
					<div class="ribbon"><span>Deal</span></div>
				</div>
			</div>
			<div class="col-md-3 brand-grid">
				<div class="card">
					<img class="img-fluid" src="/images/website/brands/13351.png" alt="Nike">
					<button class="btn view-btn">Nike</button>
				</div>
			</div>
			<div class="col-md-3 brand-grid">
				<div class="card">
					<img class="img-fluid" src="/images/website/brands/26438.png" alt="Puma">
					<button class="btn view-btn">Puma</button>
				</div>
			</div>
			<div class="col-md-3 brand-grid">
				<div class="card">
					<img class="img-fluid" src="/images/website/brands/15560.png" alt="Tory Burch">
					<button class="btn view-btn">Tory Burch</button>
					<div class="ribbon"><span>Deal</span></div>
				</div>
			</div>
			<div class="col-md-3 brand-grid">
				<div class="card">
					<img class="img-fluid" src="/images/website/brands/6712.png" alt="Zumiez">
					<button class="btn view-btn">Zumiez</button>
				</div>
			</div>
		</div>
	</div>
</section>
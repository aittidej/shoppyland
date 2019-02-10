<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */

$dashboardList = [
			[
				'label' => '<i class="fas fa-home fa-5x"></i><h4>Website</h4>',
				'link' => '/',
			],
			[
				'label' => '<i class="fab fa-product-hunt fa-5x"></i><h4>Product</h4>',
				'link' => '/product',
			],
			[
				'label' => '<i class="fas fa-book-open fa-5x"></i><h4>Open Order</h4>',
				'link' => '/openorder/order',
			],
			[
				'label' => '<i class="fas fa-user-friends fa-5x"></i><h4>Users</h4>',
				'link' => '/user',
			],
			[
				'label' => '<i class="fas fa-boxes fa-5x"></i><h4>Lots</h4>',
				'link' => '/lot',
			],
			[
				'label' => '<i class="fas fa-warehouse fa-5x"></i><h4>Stocks</h4>',
				'link' => '/stock',
			],
			[
				'label' => '<i class="fas fa-funnel-dollar fa-5x"></i><h4>Discount</h4>',
				'link' => '/discount',
			],
			[
				'label' => '<i class="fas fa-search-dollar fa-5x"></i><h4>Report</h4>',
				'link' => '/report/profit',
			]
		];
?>
<!-- FONTAWESOME STYLES-->
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
<!-- CUSTOM STYLES-->
<link href="/css/custom.css" rel="stylesheet" />
<!-- GOOGLE FONTS-->
<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />

<!-- /. NAV SIDE  -->
<div id="page-wrapper" >
	<div id="page-inner">
		<div class="row">
			<div class="col-lg-12">
			 <h2>ADMIN DASHBOARD</h2>   
			</div>
		</div>              
		<!-- /. ROW  -->
		<hr />
		
		<!--
		<div class="row">
			<div class="col-lg-12 ">
				<div class="alert alert-info">
				<strong>Welcome Jhon Doe ! </strong> You Have No pending Task For Today.
				</div>
			</div>
		</div>
		-->
		<!-- /. ROW  --> 
		<div class="row text-center pad-top">
			<?php foreach($dashboardList AS $item) { ?>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
					<div class="div-square">
						<?= Html::a($item['label'], [$item['link']]); ?>
					</div>
				</div>
			<?php } ?>
		</div>
		
	</div><!-- /. PAGE INNER  -->
</div><!-- /. PAGE WRAPPER  -->

<!-- CUSTOM SCRIPTS -->
<script src="/js/custom.js"></script>
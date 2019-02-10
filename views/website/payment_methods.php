<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'โอนเงินบัญชีธนาคาร';
$r = empty($_GET['r']) ? false : array_map('strtolower', array_map('trim', explode(",",$_GET['r'])));
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
.bank-logo { width: 20%; text-align:center; }
img.scb-logo { width: 50%; }
@media (max-width: 480px) {
    .bank-logo { width: 30%; text-align:center; }
	img.scb-logo { width: 85%; }
	.main_title { margin-top: 75px; }
}
.table td, .table th { vertical-align: middle; }
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

			<div class="table-responsive">
				<table class="table table-striped">
					<thead>
						<tr class='table-active'>
							<th>ธนาคาร</th>
							<!--<th>สาขา</th>-->
							<th>เลขบัญชี</th>
							<!--<th>ประเภทบัญชี</th>-->
							<th>ชื่อบัญชี</th>
						</tr>
					</thead>
					<tbody>
						<?php if(empty($r) || in_array('peung', $r)) { ?>
							<tr>
								<td class='bank-logo'>
									<?= Html::img('/images/bank_logo/kasikornbank.png', ['width'=>'100%']) ?>
								</td>
								<!--<td>มหาลัยรามคำแหง 2</td>-->
								<td>689-2-17516-9</td>
								<!--<td>กระแสรายวัน</td>-->
								<td>น.ส. ยุวธิดา แซ่โง้ว</td>
							</tr>
							<tr>
								<td class='bank-logo'>
									<?= Html::img('/images/bank_logo/bbl-logo.png', ['width'=>'100%']) ?>
								</td>
								<!--<td>มหาลัยรามคำแหง 2</td>-->
								<td>054-708-0523</td>
								<!--<td>กระแสรายวัน</td>-->
								<td>น.ส. ยุวธิดา แซ่โง้ว</td>
							</tr>
							<tr>
								<td class='bank-logo'>
									<?= Html::img('/images/bank_logo/scb-logo-png-5.png', ['class'=>'scb-logo']) ?>
								</td>
								<!--<td>มหาลัยรามคำแหง 2</td>-->
								<td>154-246126-3</td>
								<!--<td>กระแสรายวัน</td>-->
								<td>น.ส. ยุวธิดา แซ่โง้ว</td>
							</tr>
						<?php } ?>
						<?php if(empty($r) || in_array('ohm', $r)) { ?>
							<tr>
								<td class='bank-logo'>
									<?= Html::img('/images/bank_logo/kasikornbank.png', ['width'=>'100%']) ?>
								</td>
								<!--<td>มหาลัยรามคำแหง 2</td>-->
								<td>689-2-21345-1</td>
								<!--<td>กระแสรายวัน</td>-->
								<td>นาย อิทธิเดช ตันวิไล</td>
							</tr>
						<?php } ?>
						<?php if(empty($r) || in_array('pick', $r)) { ?>
							<tr>
								<td class='bank-logo'>
									<?= Html::img('/images/bank_logo/kasikornbank.png', ['width'=>'100%']) ?>
								</td>
								<!--<td>แฟชั่นไอล์แลนด์ รามอินทรา</td>-->
								<td>004-3-30014-8</td>
								<!--<td>กระแสรายวัน</td>-->
								<td>น.ส. เพ็ญมณี ศรีเพ็ง</td>
							</tr>
						<?php } ?>
						<?php if(empty($r) || in_array('mom', $r)) { ?>
							<tr>
								<td class='bank-logo'>
									<?= Html::img('/images/bank_logo/bbl-logo.png', ['width'=>'100%']) ?>
								</td>
								<!--<td>มหาลัยรามคำแหง 2</td>-->
								<td>054-7-16105-9</td>
								<!--<td>กระแสรายวัน</td>-->
								<td>น.ส. สุนันท์ บัวจันรทร์</td>
							</tr>
						<?php } ?>
						<?php if(empty($r) || in_array('poon', $r)) { ?>
							<tr>
								<td class='bank-logo'>
									<?= Html::img('/images/bank_logo/kasikornbank.png', ['width'=>'100%']) ?>
								</td>
								<!--<td>แฟชั่นไอล์แลนด์ รามอินทรา</td>-->
								<td>050-2-74386-4</td>
								<!--<td>กระแสรายวัน</td>-->
								<td>น.ส. ศศิพร ศรีเพ็ง</td>
							</tr>
							<tr>
								<td class='bank-logo'>
									<?= Html::img('/images/bank_logo/scb-logo-png-5.png', ['class'=>'scb-logo']) ?>
								</td>
								<!--<td>แฟชั่นไอล์แลนด์ รามอินทรา</td>-->
								<td>109-279-423-9</td>
								<!--<td>กระแสรายวัน</td>-->
								<td>น.ส. ศศิพร ศรีเพ็ง</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			
			
			
			
			
		</div>
	</div>
</section>
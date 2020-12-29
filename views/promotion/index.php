<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Lot */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Promotion';
?>
<link href='https://fonts.googleapis.com/css?family=Sofia' rel='stylesheet'>

<div class="container bg-faded">

    <h1 class="text-center" style='font-family: Sofia;'><?= Html::a('Shoppyland by Honey', 'https://shoppylandbyhoney.com/', ['style'=>'color: #000000; text-decoration: none']); ?></h1>
    <div class="row">
		<div class="col-xs-12 text-center">Line ID: <?= Html::a('@yuwatida85', 'https://line.me/ti/p/VyICG_e9u8', ['target'=>'_blank']); ?></div>
		<div class="col-xs-12 text-center">Facebook: <?= Html::a('@shoppylandbyhoney', 'https://www.facebook.com/shoppylandbyhoney', ['target'=>'_blank']); ?></div>
		<div class="col-xs-12 text-center">Instagram: <?= Html::a('@yuwatidahoney', 'https://www.instagram.com/yuwatidahoney', ['target'=>'_blank']); ?></div>
    </div>
    <hr>
    <div class="row">
		<div class="col-xs-12 text-center">
			<h2 class="text-center">
				COUPON CODE<br>
				ส่วนลด  ฿<?= number_format($promotionCode->discount_amount) ?>
			</h2>
		</div>
        <div class="col-xs-12">
            <div class="center-block well" style="width: 240px">
				<h1 class="text-center"><?= $promotionCode->used ? "<span class='text-danger'>EXPIRED</span>" : $promotionCode->code ?></h1>
            </div>
        </div>
        <div class="col-xs-12 text-center">
			<p>
				<?= empty($detail) ? ''  : '*'; ?>
				<?= $detail; ?>
			</p>
		</div>
    </div>
    <hr>
   <div class="row">
        <div class="col-xs-12 text-center">Copy หรือ Screenshot แล้วส่งมาที่ <br>Line ID: <?= Html::a('@yuwatida85', 'https://line.me/ti/p/VyICG_e9u8'); ?> นะคะ</div>
    </div>
	
</div>
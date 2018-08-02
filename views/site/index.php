<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */

$this->title = Yii::$app->name;
?>
<style>
.search {
	width: 52.5%;
	height: 44px;
	border-top: 1px solid #bdbdbd;
	border-bottom: 1px solid #d3d3d3;
	border-right: 1px solid #d3d3d3;
	border-left: 1px solid #d3d3d3;
	padding-right: 30px;
	font: 16px arial, sans-serif;
	text-indent:5px;
	background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAACrElEQVR42u2Xz2sTQRSAX8VSb1K8iNqKooJH2Ux6Ksn+iPQqxZMIehJB0do/IMhmQWsvHr2KSEGk0tSLIoWIYNUKij20F2/N7iaUZnYT0kYzzhMKs0HDJiTdLcwHDwKZSd63781LBiQSSW9JZdkhzfKm1Rz9mjZp/W9YdEU3vXv4HsQZ40FtNG36q5rls//Ej4tmbSS2T15Mvp3ExOPmEMQNbBtMMEyoljcFcQN7PqyAlqNfIG7gYQ0tYNIaxA1MrJPY3wImbUqBKAXSFv0tBSIVMOkvKRDtGKWN/T6FdqRAxFNoWwpEPIXqUqBT6ALU/UVgu8GW4GD3f6f9TRDYNJTDrk7YbtiqUumHwIYoUJuHERDAS0r4CvgFECgbY+cFAR7KT+g1POmCKFDNw6WggHc3fBtVb4CAoyauBgXIG+g1Xh5mRAGah6cggBd11fK/h7lOprIs0H6uRl6KAo5O7kOv4QmPiwJ4Jqqv4FiwCtXjvD2+tRmfK6kZ/ygI2HritK0rDVGgrClJ6DWMwYC/AGuCBMYcIC2V0CzvjmbRz3j3xUjn6CfeYreUJ2wQkGD75INPX1mFfsEFrrcIYCvdhC4paWQakxajpJMr0C9YFg54i7AsClRmh9/xnr0NHcInzZStk2aLwAcGMAD9pPIazvFKVDD5rdnhJeHLX5RTyRPQHpz5o66emMc9wdlPtvA8wF7Aq2BUHh1525qEo5JtR1WeOXpickO9cJIpyuD6xJmhYiZ5ytWSl3mlnuOaf+2zDaLDXmJrSgZ/MYVEugo+gSh+FkSBa4yd5Ul87DZ5XpFl/AyIEjzYjkau8WqshU2cr13HPbgX4gJOD97n465GZlyVvC9mSKloKI2iTnbwNT+gBX54H+IaXAtxJzE3ycSAFqSAFJACUkAikXD+AHj5/wx2o5osAAAAAElFTkSuQmCC) no-repeat -3px 0;
	background-repeat:no-repeat;
	background-position:536px 10px;
	background-size:18px 23px;
}

.button {
	background-image: -moz-linear-gradient(top,#f5f5f5,#f1f1f1);
	-moz-border-radius: 2px;
	-moz-user-select: none;
	background-color: #f2f2f2;
	border: 1px solid #f2f2f2;
	border-radius: 2px;
	color: #757575;
	cursor: default;
	font-family: arial,sans-serif;
	font-size: 13px;
	font-weight: bold;
	margin: 11px 4px;
	min-width: 54px;
	padding: 7px 16px;
	text-align: center;
}

.button:hover {
	 background-image: -moz-linear-gradient(top,#f8f8f8,#f1f1f1);
    -moz-box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    background-color: #f8f8f8;
    background-image: linear-gradient(top,#f8f8f8,#f1f1f1);
    background-image: -o-linear-gradient(top,#f8f8f8,#f1f1f1);
    border: 1px solid #c6c6c6;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    color: #222;
}

.search:hover {
	border:1px solid #aaaaaa;
}
</style>
<div class="site-index">
	<center>
		<div class='col-sm-12' style='margin-top: 14%;'>
			<div class='col-sm-12'>
				<?= Html::a(Html::img('https://www.google.com/images/srpr/logo11w.png', ['style'=>'width:270px;height:95px;margin:5px;']), ['/']); ?>
			</div>
			<div class='col-sm-12'><br></div>
			<div class='col-sm-12'>
				<?php 
					$form = ActiveForm::begin(['method'=>'GET', 'action' => ['/product']]); 
						echo Html::textInput('q', NULL, ['type'=>'search', 'class'=>'search'])."<br><br>";
						echo Html::submitButton('Google Search', ['class' => 'button', 'value'=>'submit', 'id'=>'submit']);
						echo Html::submitButton("I'm Feeling Lucky", ['class' => 'button', 'value'=>'lucky', 'id'=>'lucky']);
					 ActiveForm::end();
				 ?>
				<!--<form name="google" action="https://www.google.com/search" method="GET"><br>
					<input type="search" name='q' class="search"><br><br>
					<input type="submit" class="button" name="submit" value="Google Search">
					<input type="submit" class="button" name="lucky" value="I'm Feeling Lucky">
				</form>-->
			</div>
		</div>
	</center>
</div>

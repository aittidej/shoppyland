<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */

$this->title = Yii::$app->name;
?>

<div class="site-index">

	<?php 
		if($user->isAdmin)
			echo $this->render('_admin', ['user'=>$user]); 
		else
			echo $this->render('_client', ['user'=>$user]); 
	?>

</div>

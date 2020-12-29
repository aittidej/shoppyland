<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\OpenOrder */

$this->title = 'Update Open Order: ' . $model->open_order_id;
$this->params['breadcrumbs'][] = ['label' => 'Open Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->open_order_id, 'url' => ['view', 'id' => $model->open_order_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="open-order-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
		'upload' => $upload
    ]) ?>

</div>

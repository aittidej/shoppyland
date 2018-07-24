<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lot */

$this->title = 'Update Lot: ' . $model->lot_id;
$this->params['breadcrumbs'][] = ['label' => 'Lots', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lot_id, 'url' => ['view', 'id' => $model->lot_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lot-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

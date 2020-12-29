<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\LotRel */

$this->title = 'Update Lot Rel: ' . $model->lot_rel_id;
$this->params['breadcrumbs'][] = ['label' => 'Lot Rels', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->lot_rel_id, 'url' => ['view', 'id' => $model->lot_rel_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lot-rel-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\LotRel */

$this->title = $model->lot_rel_id;
$this->params['breadcrumbs'][] = ['label' => 'Lot Rels', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lot-rel-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->lot_rel_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->lot_rel_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'lot_rel_id',
            'lot_id',
            'product_id',
            'discount_list_id',
            'price',
            'overwrite_total',
            'creation_datetime',
            'bought_date',
            'total',
            'bought_price',
            'currency',
        ],
    ]) ?>

</div>

<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LotSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lots';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lot-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Lot', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'lot_id',
            'lot_number',
            'lotOwner',
            'creation_datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

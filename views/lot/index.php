<?php

use yii\helpers\Html;
use kartik\grid\GridView;

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
           // 'buy_date',

            [
				'class' => 'kartik\grid\ActionColumn',
				'template' => '{view} {update} {delete}',
				'width' => '8%',
				'buttons' => [
					'view' => function ($url, $model) {
						return Html::a(
							'<span class="glyphicon glyphicon-pencil"></span>',
							$url, [ 'title' => 'Edit Lot Info' ]
						);
					},
					'update' => function ($url, $model) {
						return Html::a(
							'<span class="glyphicon glyphicon-tag"></span>',
							$url, [ 'title' => "Add & Modify Lot's Items" ]
						);
					},
				],
			],
        ],
    ]); ?>
</div>

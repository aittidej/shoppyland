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
            //'lotOwner',
			'start_date:date',
			'end_date:date',
			[
				'class' => 'kartik\grid\DataColumn',
				'label' => '# of days',
				'format' => 'raw',
				'vAlign' => 'middle',
				'value' => function ($model) {
					$date1 = date_create($model->start_date);
					$date2 = date_create($model->end_date);
					$diff = date_diff($date1, $date2);
					return $diff->format("%a");
				},
			],
			
			'shipped_date:date',

            [
				'class' => 'kartik\grid\ActionColumn',
				'template' => '{view} {update} {select-by-image} {delete}',
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
					'select-by-image' => function ($url, $model) {
						return Html::a(
							'<span class="glyphicon glyphicon-plus"></span>',
							$url, [ 'title' => "Select by Image" ]
						);
					},
				],
			],
        ],
    ]); ?>
</div>

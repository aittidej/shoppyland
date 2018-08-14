<?php

use yii\web\View;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OpenOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Open Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="open-order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
	
	
	<?= GridView::widget([
        'dataProvider' => $dataProvider,
		//'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'open_order_id',
            //'lot.lot_number',
			[
        		//'attribute' => 'lot.lot_number',
        		'vAlign' => 'middle',
        		'hAlign' => 'left',
        		'pageSummary' => true,
        		'group'=>true,  // enable grouping,
        		'groupedRow'=>true,                    // move grouped column to a single grouped row
        		'groupOddCssClass'=>'kv-grouped-row',  // configure odd group cell css class
        		'groupEvenCssClass'=>'kv-grouped-row', // configure even group cell css class
				'value' => function ($model, $key, $index, $column) {
					return "Lot #".$model->lot->lot_number;
				},
        	],
            'user.name',
			[
				'class' => 'kartik\grid\DataColumn',
				'attribute' => 'creation_datetime',
				//'filterType' => GridView::FILTER_DATE,
				'filter' => false,
				'value' => function ($model, $key, $index, $column) {
					return date('M d, Y g:i A', strtotime($model->creation_datetime));
				},
				'label' => 'Date/Time',
				'format' => 'raw',
				'vAlign' => 'middle',
			],
			[
				'class' => 'kartik\grid\DataColumn',
				'attribute' => 'numberOfItems',
				'label' => 'Number of Items',
				'vAlign' => 'middle',
				'filter' => false,
				'format' => 'raw',
				'hAlign' => 'center',
				'pageSummary' => true,
				'value' => function($data) {
					return $data->numberOfItems;
				},
			],
			[
				'class' => 'kartik\grid\DataColumn',
				'attribute' => 'number_of_box',
				'vAlign' => 'middle',
				'filter' => false,
				'pageSummary' => true,
				'hAlign' => 'center',
				'format' => 'raw',
			],
			[
				'class' => 'kartik\grid\DataColumn',
				'attribute' => 'total_weight',
				'vAlign' => 'middle',
				'filter' => false,
				'pageSummary' => true,
				'format' => 'raw',
			],
            //'total_usd',
            //'total_baht',
            //'status',

			[
				'class' => 'kartik\grid\ActionColumn',
				'template' => '{add-items} {load-price} {view} {update} {delete}',
				'hAlign' => 'center', 
				'vAlign' => 'middle',
				'width' => '10%',
				'buttons' => [
					'add-items' => function ($url, $model) {
						return Html::a(
							'<i class="glyphicon glyphicon-plus"></i>',
							$url, [ 'title' => 'Add Items' ]
						);
					},
					'load-price' => function ($url, $model) {
						return "<span id='load-".$model->open_order_id."'>".Html::a(
							'<i class="glyphicon glyphicon-download-alt"></i>',
							'javascript:void(0);', [ 'title' => 'Load Price', 'class' => 'load-price', 'data-order_id' => $model->open_order_id ]
						)."</span>";
					}
				],
			],
        ],
        'headerRowOptions' => ['class' => 'kartik-sheet-style'],
        'filterRowOptions' => ['class' => 'kartik-sheet-style'],
        'id' => 'open-order-list',
        'pjax' => true,
        'toolbar' => [['content' => Html::a('<i class="glyphicon glyphicon-plus"></i> Create Open Order', ['create'], ['class' => 'btn btn-success'])]],
        'bordered' => 0,
        'striped' => 1,
        'condensed' => 1,
        'responsive' => 1,
        'hover' => 1,
        'showPageSummary' => true,
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
			'heading' => '<span id="display-filter" style=\'cursor: pointer\'><span class="view-upload-files glyphicon glyphicon-menu-down"></span>  List of Open Orders</span>',
        ],
        'persistResize' => false,
    ]);
	
	?>
</div>

<?php
$this->registerJs("
	$( '.load-price' ).click(function() {
		var orderId = $(this).data('order_id');
		$.ajax({
			url: '".Yii::$app->getUrlManager()->createUrl('openorder/order/load-price')."',
			type: 'POST',
			data: { orderId: orderId },
			beforeSend: function() {
				$('#load-'+orderId).html(\"<img src='https://loading.io/spinners/hourglass/lg.sandglass-time-loading-gif.gif' width='14px' height='14px'>\");
			},
			success: function(result) {
				console.log(result);
				if(result)
					$('#load-'+orderId).html(\"<i class='glyphicon glyphicon-ok' style='color: green;'></i>\");
				
			},
			error: function(err) {
				console.log(err);
				$('#load-'+orderId).html(\"<i class='glyphicon glyphicon-remove' style='color: red;'></i>\");
			}
		});
	});
", View::POS_READY);
?>
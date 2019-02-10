<?php
date_default_timezone_set('America/Los_Angeles');

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
			[
        		'vAlign' => 'middle',
        		'hAlign' => 'left',
        		'pageSummary' => true,
        		'group'=>true,  // enable grouping,
        		'groupedRow'=>true,                    // move grouped column to a single grouped row
        		'groupOddCssClass'=>'kv-grouped-row',  // configure odd group cell css class
        		'groupEvenCssClass'=>'kv-grouped-row', // configure even group cell css class
				'value' => function ($model, $key, $index, $column) USE ($lotsNumberOfItems) {
					$lot = $model->lot;
					return $lot->lotText." ** (".$lotsNumberOfItems[$lot->lot_id]." items)";
				},
        	],
			[
				'class' => 'kartik\grid\DataColumn',
				'label' => 'Currency',
				'format' => 'raw',
				'vAlign' => 'middle',
				'value' => function ($model, $key, $index, $column) {
					return ($model->user->currency_base == 'USD') ? "<span style='color:green;'>$$$</span>" : "<span style='color:red;'>&#3647;&#3647;&#3647;</span>";
				},
			],
			[
				'class' => 'kartik\grid\DataColumn',
				'attribute' => 'user.name',
				'label' => 'Name',
				'format' => 'raw',
				'vAlign' => 'middle',
				'value' => function ($model, $key, $index, $column) {
					//return Html::a($model->user->name, 'javasript:void(0);', ['class'=>'copy', 'data-link'=>'http://shoppylandbyhoney.com/index.php/openorder/client/preview?token='.$model->token]);
					return "<a href='http://shoppylandbyhoney.com/index.php/openorder/client/preview?token=".$model->token."' target='_blank'>".$model->user->name."</a>";
				},
			],
			/*[
				'class' => 'kartik\grid\DataColumn',
				'attribute' => 'labor_cost',
				'label' => 'Labor',
				'format' => 'raw',
				'vAlign' => 'middle',
				'value' => function ($model, $key, $index, $column) {
					return empty($model->labor_cost) ? '$0.00' : '$'.number_format($model->labor_cost, 2);
				},
			],*/
			/*[
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
			],*/
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
			[
				'class' => 'kartik\grid\BooleanColumn',
				'attribute' => 'invoice_sent',
				'label' => 'Sent', 
				'falseLabel' => 'Disabled',
				'hAlign' => 'center',
				'vAlign' => 'middle',
			],
			
            //'total_usd',
            //'total_baht',
            //'status',

			[
				'class' => 'kartik\grid\ActionColumn',
				'template' => '{add-items} {load-price} {view} {invoice} {update} {delete}',
				'hAlign' => 'center', 
				'vAlign' => 'middle',
				'width' => '25%',
				'buttons' => [
					'view' => function ($url, $model) {
						return Html::a(
							'<i class="glyphicon glyphicon-usd"></i>',
							$url, [ 'title' => 'Pricing' ]
						);
					},
					'invoice' => function ($url, $model) {
						return Html::a(
							'<i class="glyphicon glyphicon-print"></i>',
							['openorder/report', 'id'=>$model->open_order_id], [ 'title' => 'Invoice' ]
						);
					},
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
        'pjax' => false,
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
				if(result == '1')
					$('#load-'+orderId).html(\"<i class='glyphicon glyphicon-ok' style='color: green;'></i>\");
				else
					$('#load-'+orderId).html(\"<i class='glyphicon glyphicon-remove' style='color: red;'></i>\");
			},
			error: function(err) {
				console.log(err);
				$('#load-'+orderId).html(\"<i class='glyphicon glyphicon-remove' style='color: red;'></i>\");
			}
		});
	});
	
	$( '.copy' ).click(function() {
		var copyText = $(this).data('link');
		alert(copyText);
	});
", View::POS_READY);
?>
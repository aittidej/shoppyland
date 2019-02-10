<?php
date_default_timezone_set('America/Los_Angeles');

use yii\web\View;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OpenOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Order History';
//$this->params['breadcrumbs'][] = $this->title;

?>
<div class="open-order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
	
	
	<?= GridView::widget([
        'dataProvider' => $dataProvider,
		//'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
			[
				'class' => 'kartik\grid\DataColumn',
				'attribute' => 'lot.lot_number',
				'label' => 'Lot Number',
				'vAlign' => 'middle',
				'filter' => false,
				'format' => 'raw',
				'hAlign' => 'center',
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
			[
				'class' => 'kartik\grid\BooleanColumn',
				'attribute' => 'invoice_sent',
				'label' => 'Invoice Sent', 
				'falseLabel' => 'Disabled',
				'hAlign' => 'center',
				'vAlign' => 'middle',
			],
			
            //'total_usd',
            //'total_baht',
            //'status',

			[
				'class' => 'kartik\grid\ActionColumn',
				'template' => '{invoice}',
				'hAlign' => 'center', 
				'vAlign' => 'middle',
				'width' => '25%',
				'buttons' => [
					'invoice' => function ($url, $model) {
						return "<a href='http://shoppylandbyhoney.com/index.php/openorder/client/preview?token=".$model->token."' class='btn btn-warning' target='_blank'>Invoice</a>";
					},
				],
			],
        ],
        'headerRowOptions' => ['class' => 'kartik-sheet-style'],
        'filterRowOptions' => ['class' => 'kartik-sheet-style'],
        'id' => 'open-order-list',
        'pjax' => false,
        //'toolbar' => [['content' => Html::a('<i class="glyphicon glyphicon-plus"></i> Create Open Order', ['create'], ['class' => 'btn btn-success'])]],
        'bordered' => 0,
        'striped' => 1,
        'condensed' => 1,
        'responsive' => 1,
        'hover' => 1,
        'showPageSummary' => true,
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
			'heading' => '<span id="display-filter" style=\'cursor: pointer\'><span class="view-upload-files glyphicon glyphicon-menu-down"></span>  List of Orders</span>',
        ],
        'persistResize' => false,
    ]);
	
	?>
</div>
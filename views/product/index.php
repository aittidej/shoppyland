<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
	
	<?= GridView::widget([
        'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'product_id',
			[
				'class' => 'kartik\grid\DataColumn',
				'attribute' => 'image_path',
				'label' => '',
				'vAlign' => 'middle',
				'format' => 'raw',
				'width' => '150px',
				'value' => function($product) {
					return Html::img($product->firstImage, ['width'=>'100%']);;
				},
			],
            [
				'class' => 'kartik\grid\DataColumn',
				'attribute' => 'upc',
				//'label' => 'Branch',
				'vAlign' => 'middle',
			],
			[
				'class' => 'kartik\grid\DataColumn',
				'attribute' => 'model',
				//'label' => 'Branch',
				'vAlign' => 'middle',
			],
			[
				'class' => 'kartik\grid\DataColumn',
				'attribute' => 'brand.title',
				'label' => 'brand',
				'width' => '10%',
				'vAlign' => 'middle',
			],
			[
				'class' => 'kartik\grid\DataColumn',
				'attribute' => 'title',
				//'label' => 'Branch',
				'vAlign' => 'middle',
			],
            //'base_price',
            //'category',
            //'weight',
            //'status',
            //'description:ntext',
            //'color',
            //'size',
            //'dimension',
            //'image_path',
            //'json_data',

            [
				'class' => 'kartik\grid\ActionColumn',
				'hAlign' => 'center',
				'vAlign' => 'middle',
				'width' => '7%',
			],
        ],
        'headerRowOptions' => ['class' => 'kartik-sheet-style'],
        'filterRowOptions' => ['class' => 'kartik-sheet-style'],
        'id' => 'open-order-list',
        'pjax' => false,
        'toolbar' => [
			[
				'content' => Html::a('<i class="glyphicon glyphicon-plus"></i> Add Product Manually', ['create'], ['class' => 'btn btn-danger']).' '.
							Html::a('<i class="glyphicon glyphicon-plus"></i> Add Product using UPC', ['add-products-by-upc'], ['class' => 'btn btn-success'])
			]
		],
        'bordered' => 0,
        'striped' => 1,
        'condensed' => 1,
        'responsive' => 1,
        'hover' => 1,
        'showPageSummary' => false,
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
			'heading' => '<span id="display-filter" style=\'cursor: pointer\'><span class="view-upload-files glyphicon glyphicon-menu-down"></span>  List of Open Orders</span>',
        ],
        'persistResize' => false,
    ]);
	
	?>
</div>

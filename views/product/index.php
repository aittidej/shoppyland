<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\grid\GridView;
use app\models\Brand;

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
			[
				'class' => 'kartik\grid\DataColumn',
				'attribute' => 'product_id',
				'vAlign' => 'middle',
			],
			[
				'class' => 'kartik\grid\DataColumn',
				'attribute' => 'image_path',
				'label' => '',
				'vAlign' => 'middle',
				'filter' => false,
				'format' => 'raw',
				'width' => '15%',
				'value' => function($product) {
					return Html::img($product->firstImage, ['width'=>'90%']);
				},
			],
            [
				'class' => 'kartik\grid\DataColumn',
				'attribute' => 'upc',
				'vAlign' => 'middle',
			],
			[
				'class' => 'kartik\grid\DataColumn',
				'attribute' => 'model',
				'vAlign' => 'middle',
			],
			[
				'attribute' => 'brand_id',
				'value' => 'brand.title',
				'label' => 'Brand',
				'options' => [],
				'format' => 'raw',
				'vAlign' => 'middle',
				'filterType' => GridView::FILTER_SELECT2,
				'filter' => ArrayHelper::map(Brand::find()->where(['status'=>1])->orderby('title ASC')->all(), 'brand_id', 'title'),
				'filterWidgetOptions' => [
					'pluginOptions' => ['allowClear' => true]
				],
				'filterInputOptions' => ['placeholder' => 'Filter by Brand', 'class' => 'form-control']
			],
			[
				'class' => 'kartik\grid\DataColumn',
				'attribute' => 'title',
				'vAlign' => 'middle',
				'contentOptions' => [
					'style' => [
						'max-width' => '450px',
						'white-space' => 'normal',
					],
				],
				'width' => '45%',
				'format' => 'raw'
			],
			[
				'class' => 'kartik\grid\ActionColumn',
				'template' => '{add-products-ocr} {view} {update} {delete} {temp}',
				'width' => '8%',
				'buttons' => [
					'add-products-ocr' => function ($url, $model) {
						return Html::a(
							'<span class="glyphicon glyphicon-tag"></span>',
							$url, [ 'title' => 'Edit Product by Price Tags' ]
						);
					},
					'temp' => function ($url, $model) {
						if(empty($model->size))
						{
							return "<br>".Html::a('&nbsp;&nbsp;&nbsp;4&nbsp;&nbsp;&nbsp;', ['product/pick-size', 'id'=>$model->product_id, 'size'=>'S'], ['target'=>'_blank']).
								Html::a('&nbsp;&nbsp;&nbsp;5&nbsp;&nbsp;&nbsp;', ['product/pick-size', 'id'=>$model->product_id, 'size'=>'M'], ['target'=>'_blank']).
								Html::a('&nbsp;&nbsp;&nbsp;6&nbsp;&nbsp;&nbsp;', ['product/pick-size', 'id'=>$model->product_id, 'size'=>'L'], ['target'=>'_blank']);
						}
					},
				],
			],
        ],
        'headerRowOptions' => ['class' => 'kartik-sheet-style'],
        'filterRowOptions' => ['class' => 'kartik-sheet-style'],
        'id' => 'product-list',
        'pjax' => false,
        'pjaxSettings' => ['options' => ['id'=>'-pjax']],
        'toolbar' => [
			[
				'content' => Html::a('<i class="glyphicon glyphicon-plus"></i> Add Product Manually', ['create'], ['class' => 'btn btn-danger']).' '.
							Html::a('<i class="glyphicon glyphicon-plus"></i> Add Product using UPC', ['add-products-by-upc'], ['class' => 'btn btn-success']).' '.
							Html::a('<i class="glyphicon glyphicon-refresh"></i> Reset', ['/product'], ['class' => 'btn btn-default'])
			]
		],
        'bootstrap' => 1,
        'bordered' => 0,
        'striped' => 1,
        'condensed' => 1,
        'responsive' => 1,
        'responsiveWrap' => 1,
        'hover' => 1,
        'showPageSummary' => false,
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
			'heading' => '<span id="display-filter" style=\'cursor: pointer\'><span class="view-upload-files glyphicon glyphicon-menu-down"></span>  List of Products</span>',
        ],
        'persistResize' => false,
    ]);
	
	?>
</div>

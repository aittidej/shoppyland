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
            'upc',
            'model',
			'title',
            'brand.title',
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

            ['class' => 'yii\grid\ActionColumn'],
        ],
        'headerRowOptions' => ['class' => 'kartik-sheet-style'],
        'filterRowOptions' => ['class' => 'kartik-sheet-style'],
        'id' => 'open-order-list',
        'pjax' => false,
        'toolbar' => [
			[
				'content' => Html::a('Create Product Manually', ['create'], ['class' => 'btn btn-danger']).' '.
							Html::a('Create Product using UPC', ['create'], ['class' => 'btn btn-success'])
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

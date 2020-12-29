<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\grid\GridView;
use app\models\Lot;

/* @var $this yii\web\View */
/* @var $searchModel app\models\StockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Stocks';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-index">

	<?php $form = ActiveForm::begin(['method' => 'GET', 'action' => 'stock']); ?>
		<h1>
			<?= Html::encode($this->title) ?> - Lot #
			<?= Html::dropDownList(
				'lot', //name
				$lotNumber,  //select
				ArrayHelper::map($lots, 'lot_number', 'lot_number'), //items
				['onchange'=>'this.form.submit();'] //options
			  ); ?>
		</h1>
	<?php ActiveForm::end(); ?>
	
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'showPageSummary' => true,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
			[
				'class' => 'kartik\grid\DataColumn',
				'attribute' => 'image_path',
				'label' => '',
				'vAlign' => 'middle',
				'filter' => false,
				'format' => 'raw',
				'width' => '15%',
				'pageSummary' => false,
				'value' => function($model) {
					return Html::img($model['product']->firstImage, ['width'=>'90%']);
				},
			],
			[
				'class' => 'kartik\grid\DataColumn',
				'label' => 'UPC',
				'format' => 'raw',
				'pageSummary' => false,
				'value' => function($model) {
					return Html::a($model['product']->upc, ['product/update', 'id'=>$model->product_id], ['target'=>'_blank']);
				},
			],
			[
				'class' => 'kartik\grid\DataColumn',
				//'attribute' => 'test',
				'label' => 'Model',
				'format' => 'raw',
				'pageSummary' => false,
				'value' => function($model) {
					return $model['product']->model;
				},
			],
            //'product.upc',
            //'product.title',
            //'product.brand.title',
			//
			[
				'class' => 'kartik\grid\DataColumn',
				'attribute' => 'qty',
				'format' => 'raw',
				'pageSummary' => true,
			],
			[
				'class' => 'kartik\grid\DataColumn',
				'attribute' => 'current_qty',
				'format' => 'raw',
				'pageSummary' => true,
			],

            //['class' => 'yii\grid\ActionColumn'],
        ],
		'headerRowOptions' => ['class' => 'kartik-sheet-style'],
        'filterRowOptions' => ['class' => 'kartik-sheet-style'],
        'id' => 'product-list',
        'pjax' => false,
        'pjaxSettings' => ['options' => ['id'=>'-pjax']],
        'toolbar' => [],
        'bootstrap' => 1,
        'bordered' => 0,
        'striped' => 1,
        'condensed' => 1,
        'responsive' => 1,
        'responsiveWrap' => 1,
        'hover' => 1,
        //'showPageSummary' => true,
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
			'heading' => '<span id="display-filter" style=\'cursor: pointer\'><span class="view-upload-files glyphicon glyphicon-menu-down"></span>  List of Products</span>',
        ],
        'persistResize' => false,
    ]); ?>
</div>

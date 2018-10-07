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
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
			/*[
				'attribute' => 'lot_id',
				'value' => 'lot.lot_number',
				'label' => 'Lot #',
				'options' => [],
				'format' => 'raw',
				'vAlign' => 'middle',
				'filterType' => GridView::FILTER_SELECT2,
				'filter' => ArrayHelper::map(Lot::find()->orderby('lot_number ASC')->all(), 'lot_id', 'lot_number'),
				'filterWidgetOptions' => [
					'pluginOptions' => ['allowClear' => true]
				],
				'filterInputOptions' => ['placeholder' => 'Filter by Lot', 'class' => 'form-control']
			],
			[
				'attribute' => 'lot_id',
        		'vAlign' => 'middle',
        		'hAlign' => 'left',
        		'pageSummary' => true,
        		'group'=>true,  // enable grouping,
        		'groupedRow'=>true,                    // move grouped column to a single grouped row
        		'groupOddCssClass'=>'kv-grouped-row',  // configure odd group cell css class
        		'groupEvenCssClass'=>'kv-grouped-row', // configure even group cell css class
				'value' => function ($model, $key, $index, $column) {
					return $model['lot']->lotText;
				},
        	],*/
			/*[
				'class' => 'kartik\grid\DataColumn',
				'attribute' => 'image_path',
				'label' => '',
				'vAlign' => 'middle',
				'filter' => false,
				'format' => 'raw',
				'width' => '15%',
				'value' => function($model) {
					return Html::img($model['product']->firstImage, ['width'=>'90%']);
				},
			],*/
			[
				'class' => 'kartik\grid\DataColumn',
				'label' => 'UPC',
				'format' => 'raw',
				'value' => function($model) {
					return Html::a($model['product']->upc, ['product/view', 'id'=>$model->product_id], ['target'=>'_blank']);
				},
			],
			[
				'class' => 'kartik\grid\DataColumn',
				'attribute' => 'test',
				'label' => 'Model',
				'format' => 'raw',
				//'filterType' => 
				'value' => function($model) {
					return $model['product']->model;
				},
			],
            //'product.upc',
            //'product.title',
            //'product.brand.title',
            'qty',
            'current_qty',

            ['class' => 'yii\grid\ActionColumn'],
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
        'showPageSummary' => false,
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
			'heading' => '<span id="display-filter" style=\'cursor: pointer\'><span class="view-upload-files glyphicon glyphicon-menu-down"></span>  List of Products</span>',
        ],
        'persistResize' => false,
    ]); ?>
</div>

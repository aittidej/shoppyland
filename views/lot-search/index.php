<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LotRelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Find Item';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lot-rel-index">
	<div class='col-sm-12'>
		<h1><?= Html::encode($this->title) ?></h1>
	</div>
	<?= $this->render('_search', ['model' => $searchModel]); ?>
	
	<div class='col-sm-12'>
		<?= GridView::widget([
			'dataProvider' => $dataProvider,
			//'filterModel' => $searchModel,
			'responsive' => false,
			'responsiveWrap' => false,
			'columns' => [
				//['class' => 'yii\grid\SerialColumn'],

				//'lot_rel_id',
				[
					'class' => 'kartik\grid\DataColumn',
					'attribute' => 'lot.lot_number',
					'label' => 'Lot #',
					'vAlign' => 'middle',
					'hAlign' => 'center',
				],
				[
					'class' => 'kartik\grid\DataColumn',
					'attribute' => 'image_path',
					'label' => '',
					'vAlign' => 'middle',
					'filter' => false,
					'format' => 'raw',
					'width' => '15%',
					'value' => function($model) {
						$product = $model['product'];
						return Html::img($product->firstImage, ['width'=>'90%'])."<br>".$product->upc;
					},
				],
				//'discount_list_id',
				//'price',
				//'overwrite_total',
				//'creation_datetime',
				[
					'class' => 'kartik\grid\DataColumn',
					'attribute' => 'creation_datetime',
					'format' => 'raw',
					'vAlign' => 'middle',
					'hAlign' => 'center',
					'value' => function ($model) {
						return date('F j, Y', strtotime($model->creation_datetime))."<br>".date('g:i a', strtotime($model->creation_datetime));
					},
				],
				[
					'class' => 'kartik\grid\DataColumn',
					'attribute' => 'bought_date',
					'vAlign' => 'middle',
					'hAlign' => 'center',
					'format' => 'raw',
					'value' => function ($model) {
						if(empty($model->bought_date))
							return NULL;
						else
							return date("F j, Y", strtotime($model->bought_date))."<br>".date('g:i a', strtotime($model->bought_date));
					},
				],
				[
					'class' => 'kartik\grid\DataColumn',
					'attribute' => 'bought_price',
					'vAlign' => 'middle',
					'hAlign' => 'center',
				],
				//'total',
				//'currency',

				//['class' => 'yii\grid\ActionColumn'],
			],
		]); ?>
	</div>
</div>

<?php

use yii\helpers\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Lot */

$this->title = "Lot #".$model->lot_number;
$this->params['breadcrumbs'][] = ['label' => 'Lots', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lot-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <!--<p>
        <?php //Html::a('Update', ['update', 'id' => $model->lot_id], ['class' => 'btn btn-primary']); ?>
        <?php /*Html::a('Delete', ['delete', 'id' => $model->lot_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]);*/ ?>
    </p>-->

    <?= DetailView::widget([
			'model'=>$model,
			'condensed'=>true,
			'hover'=>true,
			'mode' => DetailView::MODE_EDIT,
			'panel'=>[
				'heading'=>'List of Lots',
				'type'=>DetailView::TYPE_INFO,
			],
			'attributes'=>[
				//['attribute' => 'lot_id'],
				['attribute' => 'lot_number'],
				//['attribute' => 'brand_id'],
				[
					'attribute'=>'start_date', 
					'format'=>'date',
					'type'=>DetailView::INPUT_DATE,
					'widgetOptions' => [
						'pluginOptions'=>['format'=>'yyyy-mm-dd']
					],
				],
				[
					'attribute'=>'end_date', 
					'format'=>'date',
					'type'=>DetailView::INPUT_DATE,
					'widgetOptions' => [
						'pluginOptions'=>['format'=>'yyyy-mm-dd']
					],
				],
				[
					'attribute'=>'shipped_date', 
					'format'=>'date',
					'type'=>DetailView::INPUT_DATE,
					'widgetOptions' => [
						'pluginOptions'=>['format'=>'yyyy-mm-dd']
					],
				],
			]
		]);
	?>

</div>

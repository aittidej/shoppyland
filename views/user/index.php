<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'user_id',
			'name',
            'role.title',
            'username',
			'email:email',
			'last_login',
			'payment_method',
            //'is_wholesale',
            //'exchange_rate',
            
            
            //'phone',
            //'address:ntext',
            //'creation_datetime',
            //'payment_method',
            //'is_wholesale',
            //'exchange_rate',
            //'status',

            //['class' => 'yii\grid\ActionColumn'],
			[
				'class' => 'kartik\grid\ActionColumn',
				'template' => '{send-login-info} {view} {update} {delete}',
				'hAlign' => 'center', 
				'vAlign' => 'middle',
				'width' => '25%',
				'buttons' => [
					'send-login-info' => function ($url, $model) {
						return Html::a(
							'<i class="glyphicon glyphicon-send"></i>',
							$url, [ 'title' => 'Send Login Info', 'target'=>'_blank' ]
						);
					},
				],
			],
        ],
    ]); ?>
</div>

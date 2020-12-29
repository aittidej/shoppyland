<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\LotRel */

$this->title = 'Create Lot Rel';
$this->params['breadcrumbs'][] = ['label' => 'Lot Rels', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lot-rel-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

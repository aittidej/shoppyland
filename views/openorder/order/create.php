<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\OpenOrder */

$this->title = 'Create Open Order';
$this->params['breadcrumbs'][] = ['label' => 'Open Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="open-order-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'upload' => $upload,
    ]) ?>

</div>

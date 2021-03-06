<?php

use yii\helpers\Html;
use yii\widgets\DetailView;



/* @var $this yii\web\View */
/* @var $model common\models\Blog */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Blogs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="blog-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>


    <h2><?=$model->title ?></h2>
    <hr>
    <h3><?=$model->description ?></h3>
    <?=
    Html::img('/uploads/' .$model->image, ['width'=>'200px'])
    ?>
    <p><?=$model->text ?></p>
    <?php
        //$created_at = DateTime::createFromFormat('d-m-Y', $model->created_at);
    $date = new DateTime();
    echo $date->setTimestamp($model->created_at)->format('Y-m-d H:i:s');
    //echo $date->format('U = Y-m-d H:i:s') . "\n";
    ?>
    <?php

    ?>

<!--    --><?//= DetailView::widget([
//        'model' => $model,
//        'attributes' => [
//            'id',
//            'title',
//            'description:ntext',
//            'text:ntext',
//            'status',
//            'image',
//            'created_at',
//        ],
//    ]) ?>

</div>

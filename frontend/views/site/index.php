<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="body-content">

        <div class="row">
            <div class="col-lg-9">

                <?php foreach($articles as $article): ?>

                <h3><?=HTML::tag('a', $article->title, ['href' => ''.$article->seourl]) ?></h3>
                <?= HTML::img('uploads/'.$article->image, ['width'=> 100, 'height'=>'auto',]) ?>
                <p><?=$article->description?></p>
                <p><?=date( 'H:i d-m-Y', $article->created_at)?></p>
                <p><?=$article->author->username?></p>
                <hr>

                <?php endforeach; ?>

            </div>
            <div class="col-lg-3">
                <h2>to do</h2>

                <p> -> популярные новости этой недели + список популярных тегов, например 10 шт.</p>

            </div>
        </div>

    </div>
</div>

<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */

//$this->title = 'My Yii Application';
$this->registerMetaTag(['name' => 'title', 'content' => Yii::$app->user->identity->username.'`s Blog']);
$this->registerMetaTag(['name' => 'description', 'content' => 'Blog with articles and tags']);

?>
<div class="site-index">

    <div class="body-content">

        <div class="row">
            <div class="col-lg-12">
                <h2><?=Yii::$app->user->identity->username?>`s articles.</h2>
                <?php foreach($articles as $article): ?>

                    <h3><?=HTML::tag('a', $article->title, ['href' => '/site/'.$article->seourl]) ?></h3>
                    <?= HTML::img('uploads/'.$article->image, ['width'=> 100, 'height'=>'auto',]) ?>
                    <p><?=$article->description?></p>
                    <p><?=date( 'H:i d-m-Y', $article->created_at)?></p>
                    <p><?=$article->author->username?></p>
                    <?php $i = 0; $length = count($article->blogTag);?>
                    <?php foreach ($article->blogTag as $rel): ?>

                        <?php
                        if($i == $length-1){
                            echo '#'.$rel->tag->tag_name;
                        }else{
                            echo '#'.$rel->tag->tag_name.', ';
                        }
                        $i++;
                        ?>

                    <?php endforeach; ?>
                    <hr>

                <?php endforeach; ?>

            </div>

        </div>

    </div>
</div>

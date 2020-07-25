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

                <h3><?=HTML::tag('a', $article->title, ['href' => 'site/'.$article->seourl]) ?></h3>
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
            <div class="col-lg-3">
                <?php foreach($mpip as $item):?>
                    <h4><?=HTML::tag('a', $item['title'], ['href' => 'site/'.$item['seourl']]) ?></h4>
                    <p><?=$item['description'] ?></p>
                <?php endforeach;?>
                <hr>
                <div class="most_rel_tags">
                    <h5>5 самых популярных тегов</h5>
                    <div class="most_rel_tags">
                        <?php
                        foreach ($tags_names as $tag_n){
                            echo '<span>#'.$tag_n['tag_name'].'</span><br>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

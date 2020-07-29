<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
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
                    <?php if($article->is_checked == 0){?>
                        <?php if($article->in_check == 1){
                            $to_check = 'hidden';
                            $recall_check = 'visible';
                        }else{
                            $to_check = 'visible';
                            $recall_check = 'hidden';
                        } ?>
                        <div style="float: right">
                            <?php $form_to = ActiveForm::begin(['options' => [
                                'class'=>'to_check '.$to_check
                                ]
                            ]);?>
                            <?= Html::hiddenInput('blog_id', $article->id) ?>
                            <?= Html::submitButton('Send to check', ['class' => 'btn btn-primary']) ?>
                            <?php ActiveForm::end();    ?>

                            <?php $form_from = ActiveForm::begin(['options' => [
                                'class'=>'recall_check '.$recall_check
                                ]
                            ]);?>
                            <?= Html::hiddenInput('blog_id', $article->id) ?>
                            <?= Html::submitButton('Recall check', ['class' => 'btn btn-warning']) ?>
                            <?php ActiveForm::end();    ?>
                        </div>
                    <?php }?>


                    <h3><?=HTML::tag('a', $article->title, ['href' => '/site/'.$article->seourl]) ?></h3>
                    <?php if($article->image !== ''){
                        echo HTML::img('/uploads/'.$article->image, ['width'=> 100, 'height'=>'auto',]);
                    } ?>
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

<?php
$js = <<<JS
        $('form.to_check').on('beforeSubmit', function(){
            var form = $(this);
            var data = form.serialize();
            var id = $(this).find('input[name=blog_id]').val();
            
        $.ajax({
            url: '/blog/to-check?id='+id,
            type: 'POST',
            data: data,
            success: function(data){
                
                form.removeClass('visible').addClass('hidden');
                form.siblings('.recall_check').removeClass('hidden').addClass('visible');
            },
            
            error: function(){
                alert('Error!');
            }
        }).done(function(data) {
       if(data.success) {
          // данные сохранены
          console.log(data);
        } else {
          // сервер вернул ошибку и не сохранил наши данные
        }
    });
        return false;
    });

$('form.recall_check').on('beforeSubmit', function(){
            var form = $(this);
            var data = form.serialize();
            var id = $(this).find('input[name=blog_id]').val();
            console.log(id);
        $.ajax({
            url: '/blog/from-check?id='+id,
            type: 'POST',
            data: data,
            success: function(data){
                form.removeClass('visible').addClass('hidden');
                form.siblings('.to_check').removeClass('hidden').addClass('visible');
            },
            
            error: function(){
                alert('Error!');
            }
        }).done(function(data) {
       if(data.success) {
          // данные сохранены
          console.log(data);
        } else {
          // сервер вернул ошибку и не сохранил наши данные
        }
    });
        return false;
    });


JS;

$this->registerJs($js);
?>

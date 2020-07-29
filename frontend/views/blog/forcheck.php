<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

//$this->title = 'My Yii Application';
$this->registerMetaTag(['name' => 'title', 'content' => 'Articles for check']);
$this->registerMetaTag(['name' => 'description', 'content' => 'Blog with articles for check']);

?>
<div class="site-index">

    <div class="body-content">

        <div class="row">
            <div class="col-lg-12">
                <h2>Articles for check</h2>
                <?php foreach($articles as $article): ?>
                    <div class="article_wrap">
                    <?php if($article->is_checked == 0){ ?>
                    <div style="float: right">
                        <?php $form = ActiveForm::begin(['options' => [
                            'class'=>'is_checked '
                        ]
                        ]);?>
                        <?= Html::hiddenInput('blog_id', $article->id) ?>
                        <?= Html::submitButton('verified', ['class' => 'btn btn-primary']) ?>
                        <?php ActiveForm::end();    ?>



                        <?=Html::button('Decline popup',
                        [
                        'title' => 'Decline to print an article',
                        'data-toggle'=>'modal',
                        'data-target'=>'#modalvote'.$article->id,

                        ]
                        );?>

                        <!-- Modal -->
                        <div id="<?='modalvote'.$article->id?>" class="modal fade" role="dialog">
                            <div class="modal-dialog">

                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>

                                    </div>
                                    <div class="modal-body">
                                        <?php $form_d = ActiveForm::begin([ 'options' => [
                                            'class'=>'decline '
                                        ]
                                        ]);?>
                                        <?= Html::hiddenInput('blog_id', $article->id) ?>

                                        <?= Html::textarea('decline_text', 'decline text insert here', ['class' => 'form-control'])?>
                                        <?= Html::submitButton('decline', ['class' => 'btn btn-primary']) ?>
                                        <?php ActiveForm::end();    ?>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>

                            </div>
                        </div>





                    </div>
                    <?php }?>

                    <h3><?=HTML::tag('a', $article->title, ['href' => '/site/'.$article->seourl]) ?></h3>
                    <?= HTML::img('/uploads/'.$article->image, ['width'=> 100, 'height'=>'auto',]) ?>
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
                    </div>
                <?php endforeach; ?>


            </div>
        </div>

    </div>
</div>

<?php
$js = <<<JS
        $('form.is_checked').on('beforeSubmit', function(){
            var form = $(this);
            var data = form.serialize();
            var id = $(this).find('input[name=blog_id]').val();
            
        $.ajax({
            url: '/blog/verified?id='+id,
            type: 'POST',
            data: data,
            success: function(data){
                console.log(form.closest('.article_wrap'));
                form.closest('.article_wrap').remove();
                
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


    $('form.decline').on('beforeSubmit', function(){
            var form = $(this);
            var data = form.serialize();
            var id = $(this).find('input[name=blog_id]').val();
            
        $.ajax({
            url: '/blog/decline?id='+id,
            type: 'POST',
            data: data,
            success: function(data){
                $('#modalvote'+id).modal('hide');
                setTimeout(function() {
                  form.closest('.article_wrap').remove();
                }, 500);
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

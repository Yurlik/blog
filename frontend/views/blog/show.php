<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;


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
    Html::img('/uploads/' .$model->image, ['width'=>'300px'])
    ?>
    <p><?=$model->text ?></p>

    <span class="badge">
    <?php
        //$created_at = DateTime::createFromFormat('d-m-Y', $model->created_at);
    $date = new DateTime();

    echo $date->setTimestamp($model->created_at)->format('Y-m-d H:i:s');
    //echo $date->format('U = Y-m-d H:i:s') . "\n";
    ?>
    </span>

    <span class="badge"><?php
        echo 'Author: ' . $model->author->username;
    ?></span>

    <div class="tags_wrap flex-row">
        <?php foreach($model->blogTag as $rel): ?>
            <?php echo '<span class="tag">#'.$rel->tag->tag_name."</span>"?>
        <?php endforeach; ?>
    </div>

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

    <div class="comments_block">
        <h3 class="comments_title">Оставьте комментарий:</h3>

        <?php $form = ActiveForm::begin();?>

        <?=$form->field($comment, 'message_owner')->textInput();   ?>
        <?=$form->field($comment, 'message')->textarea(['rows' => 3]);  ?>

        <?= Html::hiddenInput('Comment[blog_id]', $model->id) ?>

        <?= Html::submitButton('Send', ['class' => 'btn btn-success']) ?>

        <?php ActiveForm::end();    ?>

        <div class="comments_list">
            <h3>Комментарии:</h3>
            <div class="comments_list_ins"></div>
            <?php foreach($comments as $com): ?>
                <?='<div class="comment" style="margin: 10px; border: 1px solid #ccccff; padding: 5px;">'; ?>
                    <?='<div class="mess_owner" style="margin-bottom: 10px">'.$com->message_owner.'</div>'; ?>
                    <?='<div class="mess" style="margin-bottom: 10px">'.$com->message.'</div>'; ?>
                <?='</div>'?>
            <?php endforeach; ?>
        </div>
    </div>

</div>




<?php
$js = <<<JS
        $('form').on('beforeSubmit', function(){
            var form = $(this);
            var data = form.serialize();
        $.ajax({
            url: '/comment/save',
            type: 'POST',
            data: data,
            success: function(data){
                form[0].reset();
                $(".comments_list_ins").prepend('<div class="comment" style="margin: 10px; border: 1px solid #ccccff; padding: 5px;">' +
                 '<div class="mess_owner" style="margin-bottom: 10px">'+ data.Comment.message_owner +'</div><div class="mess" style="margin-bottom: 10px">'+ data.Comment.message +'</div></div>');
                $('#process').fadeOut();
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

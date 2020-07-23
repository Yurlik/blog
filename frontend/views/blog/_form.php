<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\Tag;

/* @var $this yii\web\View */
/* @var $model common\models\Blog */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
//var_dump($model);
//echo '<hr>';
//var_dump($model);
//echo '<hr>';die;
?>

<div class="blog-form">

    <?php $form = ActiveForm::begin([
            'options' => ['enctype'=>'multipart/form-data']
    ]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'seourl')->textInput() ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 2]) ?>

    <?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->dropDownList(['off', 'on']) ?>

    <?= $form->field($model, 'image')->fileInput() ?>

    <?php

    echo $form->field($model, 'tags_array')->widget(Select2::classname(), [
//        'data' => ArrayHelper::map(Tag::find()->all(), 'id','tag_name'),
//        'data' => (new Tag)->getAllTags(),
        'data' => $tag_arr,
        'options' => ['placeholder' => 'Select tag ...', 'multiple' => true],
        'pluginOptions' => [
            'tags' => true,
            'tokenSeparators' => [',', ' '],
            'maximumInputLength' => 10
        ],
    ])->label('Tag Multiple');

    ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="tags_wrap flex-row">
        <?php foreach($model->blogTag as $rel): ?>
            <?php echo '<span class="tag">#'.$rel->tag->tag_name."</span>"?>
        <?php endforeach; ?>
    </div>
    <div class="tags_wrap flex-row">
        <?php foreach($model->blogTag as $rel): ?>
            <?php echo '<span class="tag">#'.$rel->tag->tag_name."</span>"?>
        <?php endforeach; ?>
    </div>
    <?php







    ?>

</div>

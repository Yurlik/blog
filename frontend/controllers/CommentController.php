<?php

namespace frontend\controllers;

use common\models\Comment;
use Yii;
use yii\web\Response;

class CommentController extends \yii\web\Controller
{
    public function actionSave()
    {
        $model = new Comment();
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if(Yii::$app->request->isAjax){
            if($model->load(Yii::$app->request->post())){
                $model->save();
            }
        }
        return Yii::$app->request->post();
    }



}

<?php

namespace frontend\controllers;


use Yii;
use common\models\Blog;
use common\models\BlogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * BlogController implements the CRUD actions for Blog model.
 */
class BlogController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Blog models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Blog();

        $searchModel = new BlogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Blog model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionShow($url){
//        var_dump($url);die;
        if($model = Blog::find()->andWhere(['seourl'=>$url])->one()){
            return $this->render('show', [
                'model' => $model,
            ]);
        }
        throw new NotFoundHttpException('this "'.$url.'" article is not found');

    }

    protected function findModel($id)
    {
//        if (($model = Blog::findOne($id)) !== null) {
        if (($model = Blog::find()->with('tags')->where(['id'=>$id])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    /**
     * Creates a new Blog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Blog();

        if ($model->load(Yii::$app->request->post())) {

            if($model->description == ''){
                $model->description = substr($model->text, 0, 20) . '...';
            }

            $model->user_id = Yii::$app->user->identity->id;
            $model->created_at = time();

            $model->upload();

            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $exist_img = $model->image;

        if ($model->load(Yii::$app->request->post())) {
            if($model->description == ''){
                $model->description = substr($model->text, 0, 20) . '...';
            }

            $model->created_at = time();
            if($model->upload()){

            }else{
                $model->image = $exist_img;
            }
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
}

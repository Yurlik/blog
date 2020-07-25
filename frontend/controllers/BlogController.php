<?php

namespace frontend\controllers;


use common\models\BlogTag;
use common\models\Tag;
use common\models\Visit;
use Yii;
use common\models\Blog;
use common\models\BlogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use common\models\Comment;

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
        $client_agent = Yii::$app->request->getUserAgent();
        $client_ip = Yii::$app->request->getUserIP();

        $comment = new Comment();

        if($model = Blog::find()->andWhere(['seourl'=>$url])->one()){

            $as_visit = Visit::find()->where(['client_agent'=>$client_agent])->andWhere(['client_ip'=>$client_ip])->andWhere(['blog_id'=>$model->id])->count();
//var_dump($as_visit);die;
            if($as_visit == 0){
//                var_dump($as_visit);
                Blog::updateAll(['unic_client'=>$model->unic_client+1], ['id'=>$model->id]);
                $visit = new Visit();
                $visit->blog_id = $model->id;
                $visit->client_ip = $client_ip;
                $visit->client_agent = $client_agent;
                $visit->save();
            }

            /*most pop blog in period*/
            $mpip = (new Blog())->getMostPopInPeriod(3, 7);

            /*most pop tags*/
            $tags_names = (new BlogTag())->getMostPopTags(5);
//var_dump($tags_names);die;
            /*comments*/
            $comments = Comment::find()->where(['blog_id'=>$model->id])->orderBy(['id' => SORT_DESC ])->all();

            return $this->render('show', [
                'model' => $model,
                'comment' => $comment,
                'comments' => $comments,
                'mpip' => $mpip,
                'tags_names' =>$tags_names,
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
        $tag_model = new Tag();
        $tag_arr = $tag_model->getAllTags();

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
            'tag_arr' => $tag_arr,
        ]);
    }

    public function actionUpdate($id)
    {
        $tag_model = new Tag();
        $tag_arr = $tag_model->getAllTags();

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
            'tag_arr' => $tag_arr,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
}

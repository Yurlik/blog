<?php

namespace frontend\controllers;

use yii\helpers\Url;
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
use yii\filters\AccessControl;
use common\models\User;

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
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'create', 'view', 'update', 'delete', 'forcheck', 'myblog'],
                'rules' => [
                    [
                        'actions' => ['index','create','view','myblog'],
                        'allow' => true,
                        'roles' => ['user'],
                    ],
                    [
                        'actions' => ['update', 'delete', 'forcheck'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
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

    public function actionMyblog()
    {
        $articles = Blog::find()->andWhere(['user_id' => Yii::$app->user->id ])->all();

        return $this->render('myblog', [
            'articles' => $articles,
        ]);
    }

    public function actionForcheck()
    {
        $articles = Blog::find()->andWhere(['in_check' => 1])->andWhere(['is_checked' => 0])->all();

        return $this->render('forcheck', [
            'articles' => $articles,
        ]);
    }

    public function actionToCheck($id)
    {
        if($model = Blog::find()->andWhere(['id'=>$id])->one()) {
            $model::updateAll(['in_check' => 1], ['id'=>$id]);
        }
        return true;
    }
    public function actionFromCheck($id)
    {
        if($model = Blog::find()->andWhere(['id'=>$id])->one()) {
            $model::updateAll(['in_check' => 0], ['id'=>$id]);

        }
        return true;
    }
    public function actionVerified($id)
    {
        if($model = Blog::find()->andWhere(['id'=>$id])->one()) {
            $model::updateAll(['is_checked' => 1, 'in_check' => 0, 'status' => 1], ['id'=>$id]);
            $user = User::find()->where(['id'=>$model->user_id])->one();
            //var_dump($user->email);die;
            Yii::$app->mailer->compose()
                ->setFrom('yuriycheryavski@gmail.com')
                ->setTo($user->email)
                ->setSubject('Ваша статья отмодерирована и добавлена')
                ->setTextBody('Текст сообщения')
                ->setHtmlBody('Ваша статья "'.$model->title.'" отмодерирована и добавлена')
                ->send();
        }
        return true;
    }
    //decline
    public function actionDecline($id)
    {
        if($model = Blog::find()->andWhere(['id'=>$id])->one()) {
            $model::updateAll(['is_checked' => 0, 'in_check' => 0, 'status' => 0], ['id'=>$id]);
            $user = User::find()->where(['id'=>$model->user_id])->one();

            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            if(Yii::$app->request->isAjax){
                Yii::$app->mailer->compose()
                    ->setFrom('yuriycheryavski@gmail.com')
                    ->setTo($user->email)
                    ->setSubject('Ваша статья отклонена')
                    ->setTextBody('Текст сообщения')
                    ->setHtmlBody('Ваша статья "'.$model->title.'" отклонена ибо '.Yii::$app->request->post('decline_text'))
                    ->send();
            }
        }
        return true;
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

        $client_agent = Yii::$app->request->getUserAgent();
        $client_ip = Yii::$app->request->getUserIP();

        $comment = new Comment();

        if($model = Blog::find()->andWhere(['seourl'=>$url])->one()){

            $as_visit = Visit::find()->where(['client_agent'=>$client_agent])->andWhere(['client_ip'=>$client_ip])->andWhere(['blog_id'=>$model->id])->count();

            if($as_visit == 0){

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
        $model->status = 0;
        if ($model->load(Yii::$app->request->post())) {

            if($model->description == ''){
                $model->description = substr($model->text, 0, 20) . '...';
            }

            $model->user_id = Yii::$app->user->identity->id;
            $model->created_at = time();

            $model->upload();

            if($model->save()){
                //mail('yuserche@gmail.com', 'Тема письма', 'Текст письма', 'From: yuriycheryavski@gmail.com');
                Yii::$app->mailer->compose()
                    ->setFrom('yuriycheryavski@gmail.com')
                    ->setTo('yuserche@gmail.com')
                    ->setSubject('Создана новость, ожидает модерации')
                    ->setTextBody('Текст сообщения')
                    ->setHtmlBody('Id: '.$model->id.'. url: <a href="'.Url::base(true).'/blog/update?id='.$model->id.'">'.$model->title.'</a>')
                    ->send();
            }
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

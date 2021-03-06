<?php

namespace common\models;

use Yii;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;


/**
 * This is the model class for table "blog".
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string|null $text
 * @property string|null $seourl
 * @property int|null $status
 * @property string|null $image
 * @property string|null $created_at
 */
class Blog extends \yii\db\ActiveRecord
{

    public $tags_array;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'blog';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['description', 'text'], 'string'],
            [['status'], 'integer'],
            [['created_at'], 'trim'],
//            [['title', 'image'], 'string', 'max' => 255],
            [['title'], 'unique'],

            [['image'], 'file',
                'extensions' => ['jpg', 'jpeg', 'png', 'gif'],
                'checkExtensionByMimeType' => true,

            ],
            [['seourl'], 'trim'],
            [['user_id'], 'trim'],
            [['tags_array'], 'safe'],

        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'text' => 'Text',
            'status' => 'Status',
            'image' => 'Image',
            'created_at' => 'Created At',
            'seourl' => 'ЧПУ',
            'tags_array' => 'Все теги',
        ];
    }


    public function upload()
    {

        $this->image = UploadedFile::getInstance($this, 'image');

        if(($this->image)){
            $image_file_name = rand(0, 9999).'.'.$this->image->getExtension();

            $this->image->saveAs( Yii::getAlias('@uploads'). '\\' . $image_file_name);
            $this->image = $image_file_name;

            return $this->image;
        }
        return false;
    }

    public function getAuthor(){
        return $this->hasOne(User::class, ['id'=>'user_id']);
    }


    public function getBlogTag(){
        return $this->hasMany(BlogTag::class, ['blog_id'=>'id']);
    }

    public function getTags(){
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])->via('blogTag');
    }

    public function afterFind()
    {
        $this->tags_array = $this->tags;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub

        $old_arr = ArrayHelper::map($this->tags, 'id','id');


        if(is_array($this->tags_array)){
            foreach($this->tags_array as $singl_tag){
                if(!in_array($singl_tag, $old_arr)){
                    $model = new BlogTag();
                    $model->blog_id = $this->id;
                    $model->tag_id = $singl_tag;
                    $model->save();
                }
                if(isset($old_arr[$singl_tag])){
                    unset($old_arr[$singl_tag]);
                }
            }

            BlogTag::deleteAll(['tag_id'=>$old_arr, 'blog_id'=>$this->id]);
        }else{
            BlogTag::deleteAll(['blog_id'=>$this->id]);
        }



    }



    // search most popular blogs in period

    public function getMostPopInPeriod($limit, $days){
        $time = time() - 86400*$days;
        return Blog::find()->where(['status' => 1])->andWhere('created_at > '.$time.'')->orderBy(['unic_client'=>SORT_DESC ])->asArray()->limit($limit)->all();
    }


    protected function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }


}

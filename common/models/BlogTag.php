<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "blog_tag".
 *
 * @property int $id
 * @property int $tag_id
 * @property int $blog_id
 */
class BlogTag extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'blog_tag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tag_id', 'blog_id'], 'required'],
            [['tag_id', 'blog_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tag_id' => 'Tag ID',
            'blog_id' => 'Blog ID',
        ];
    }

    public function getTag(){
        return $this->hasOne(Tag::class, ['id'=>'tag_id']);
    }


    public function getTagById($id){
        return $this->hasOne(Tag::class, ['id'=>$id]);
    }



    /*most pop tags*/
    public function getMostPopTags($limit){

        $query = (new \yii\db\Query())->select([ 'tag_id'])->from('blog_tag')->groupBy('tag_id')->orderBy('COUNT(*) DESC');
        $tags_names = (new \yii\db\Query())->select('tag_name')->from('tag')->where(['id' => $query])->limit($limit)->all();

        return $tags_names;

    }

}

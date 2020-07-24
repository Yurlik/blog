<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "comments".
 *
 * @property int $id
 * @property int $blog_id
 * @property string $message
 * @property string $message_owner
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['blog_id', 'message', 'message_owner'], 'required'],
            [['blog_id'], 'integer'],
            [['message'], 'string'],
            [['message_owner'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'blog_id' => 'Blog ID',
            'message' => 'Message',
            'message_owner' => 'Message Owner',
        ];
    }
}

<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "visit".
 *
 * @property int $id
 * @property int $blog_id
 * @property string $client_ip
 * @property string $client_agent
 */
class Visit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'visit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['blog_id', 'client_ip', 'client_agent'], 'required'],
            [['blog_id'], 'integer'],
            [['client_agent'], 'string'],
            [['client_ip'], 'string', 'max' => 50],
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
            'client_ip' => 'Client Ip',
            'client_agent' => 'Client Agent',
        ];
    }
}

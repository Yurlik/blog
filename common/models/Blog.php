<?php

namespace common\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "blog".
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string|null $text
 * @property int|null $status
 * @property string|null $image
 * @property string|null $created_at
 */
class Blog extends \yii\db\ActiveRecord
{
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
        ];
    }


    public function upload()
    {
        $this->image = UploadedFile::getInstance($this, 'image');

        $image_file_name = rand(0, 9999).'.'.$this->image->getExtension();

        $this->image->saveAs( Yii::getAlias('@uploads'). '\\' . $image_file_name);
        $this->image = $image_file_name;

        return $this->image;
    }

}

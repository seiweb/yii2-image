<?php


namespace seiweb\image\behaviors;

use seiweb\image\models\Image;
use seiweb\image\Module;
use yii\base\Behavior;
use yii\base\BootstrapInterface;
use yii\db\ActiveRecord;


/**
 * Class FileBehavior
 * @package seiweb\image\behaviors
 * @mixin ImageBehavior
 */
class ImageBehavior extends Behavior
{
    public $modelKey = null;
    public $attribute;
    public $multiple = true;


    /**
     * Возвращает изображения, привязанные к модели
     *
     * @param null $model_attribute
     * @param bool $one
     *
     * @return mixed
     */
    public function getImages($model_attribute = null, $one = false)
    {
        $q = $this->owner->hasMany(Image::className(), ['model_id' => 'id'])
            ->where(Image::tableName() . '.model_class=:m_name', [':m_name' => $this->owner->className()])->orderBy('sort');
        if ($model_attribute)
            $q->andWhere(['model_attribute' => $model_attribute]);
        if ($one)
            $q->limit(1);

        return $q;
    }


    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_DELETE => function ($event) {
                $this->deleteAllFiles();
            },
        ];
    }

    public function deleteAllFiles()
    {
        $condition = ['and', 'model_id=:id', 'model_class=:m'];
        $params = ['id' => $this->owner->primaryKey, ':m' => $this->owner->className()];

        foreach ($this->owner->images as $image) {
            $image->delete();
        }
    }

    public function attach($owner)
    {
        parent::attach($owner);
        \Yii::$app->getModule('swb_image');
    }

    public function attachFile($file, $model_attribute = 0, $replace = false)
    {
        $file->model_class = $this->owner->className();
        $file->model_attribute = $model_attribute;
        $file->model_id = $this->owner->primaryKey;


        if ($replace) {
            $files = $this->owner->getFiles($model_attribute)->all();
            foreach ($files as $f)
                $f->delete();
        }

        if ($file->save()) return true;

        return false;
    }

    public function detachImage($image)
    {
        $image->delete();
    }


    public function canGetProperty($name, $checkVars = true)
    {
        if ($name == $this->attribute) {
            return true;
        }
        return parent::canGetProperty($name, $checkVars);
    }

    public function __get($name)
    {
        if ($name == $this->attribute) {
            if ($this->multiple)
                $res = $this->getImages($this->attribute)->all();
            else
                $res = $this->getImages($this->attribute)->one();


            return $res;
        }

        return parent::__get($name); // TODO: Change the autogenerated stub
    }
}
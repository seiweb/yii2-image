<?php
namespace seiweb\image\models;

use seiweb\image\Module;
use seiweb\image\ModuleTrait;
use seiweb\image\storage\Storage;
use seiweb\sortable\behaviors\SortableGridBehavior;
use yii\base\Exception;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\BaseFileHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%swb_image}}".
 *
 * @property integer $id
 * @property integer $model_id
 * @property string  $model_class
 * @property integer $model_attribute
 * @property string  $mime
 * @property string  $file_name
 * @property string  $uf_file_name
 * @property integer $size
 * @property integer $sort
 * @property string  $ext
 * @property string  $created_at
 * @property string  $updated_at
 */
class Image extends \yii\db\ActiveRecord
{
    private $storage;

    public function init()
    {
        parent::init();
        $this->storage = Module::getInstance()->storage;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%swb_image}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => SortableGridBehavior::className(),
                'sortableAttribute' => 'sort',
                'scopeAttribute' => ['model_id', 'model_class', 'model_attribute']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['model_id', 'mime', 'file_name', 'size', 'ext'], 'required'],
            [['model_id','size', 'sort','version'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            //[['created_at'], 'date','format'=>'php:d.m.Y H'],
            [['model_class', 'mime', 'file_name'], 'string', 'max' => 255],
            [['title', 'position'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 1024],
            [['ext'], 'string', 'max' => 6],
            [['model_attribute'],'default'],
            //[['file'], 'file','maxSize'=>$this->getModule()->originalMaxSize]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model_id' => 'Id Object', //primaryKey модели, к которой привязываются файлы
            'model_class' => 'Model Key', //текстовый идентификатор модели (имя класса или название таблицы)
            'model_attribute' => 'Group Key', //кастомная подгруппа в пределах экземпляра модели (например доступ к файлам Инструкция, Сертификат качества можно будет получить непосредственно)
            'mime' => 'Mime',
            'file_name' => 'File Name', //Имя файла в фс сервера
            'title' => 'Название',
            'description' => 'Описание',
            'sort' => 'Sort',
            'ext' => 'Ext',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function delete()
    {
        $res =  $this->storage->delete($this);
        $res = parent::delete();
        return $res;
    }

    public function getFullSizeUrl()
    {
        $itemUrl =Url::to(Module::getInstance()->baseUrl.$this->storage->cacheDir.$this->storage->getSubDirectory($this));

        $res = $itemUrl
            . $this->storage->getFullSizeFile($this).'?'.$this->version;
        return $res;
    }

    public function getOriginalUrl()
    {
        $itemUrl=Url::to(Module::getInstance()->baseUrl.$this->storage->originalDir.$this->storage->getSubDirectory($this));

        $res = $itemUrl
            . $this->file_name;
        return $res;
    }

    public function getFitUrl($width, $height, $position = null)
    {
        if ($position == null)
            $position = $this->position;

        if ($position == null)
            $position = 'center';

        $itemUrl=Url::to(Module::getInstance()->baseUrl.$this->storage->cacheDir.$this->storage->getSubDirectory($this));

        $res = $itemUrl
            . $this->storage->getFitFile($this,$width,$height,$position).'?'.$this->version;
        return $res;
    }

    public function getResizedUrl($width, $height, $keep_aspect_ratio = true)
    {
        $itemUrl=Url::to(Module::getInstance()->baseUrl.$this->storage->cacheDir.$this->storage->getSubDirectory($this));
        $res = $itemUrl
            . $this->storage->getResizedFile($this,$width, $height, $keep_aspect_ratio).'?'.$this->version;

        return $res;
    }

    public function rotate($angle)
    {
        $this->storage->rotate($this,$angle);
        $this->updateCounters(['version'=>1]);
    }
}

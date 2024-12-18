<?php

namespace seiweb\image;


use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use seiweb\image\storage\Storage;
use Yii;


class Module extends \yii\base\Module
{
    public $baseUrl = '/uploads/images/';
    /**maximum upload file size in Kb
     * @var int
     */
    public $uploadFileMaxSize = 2028*1024; //2MB
    public $allowedFileExtensions = ['png', 'jpg'];
    public $maxFileCount = 10;

    /**
     * Ужать исходное изображение
     * @var int|null
     */
    public $originalResizeTo = [1024,768]; //or null
    public $originalQuality = 85;
    public $cachedQuality = 70;

    public $controllerNamespace = 'seiweb\image\controllers';

    public $watermarkFilePath = null;
    public $watermarkPosition = ['center', 0, 0];
    public $watermarkOpacity = 30;

    //конвертироваль ли все в jpg при загрузке
    public $convertToJpg = true;


    public $components;
    public $storage;
    public $imageManager;

    public function init()
    {
        parent::init();
        $this->storage = Yii::createObject(isset($this->components['storage'])?$this->components['storage']:['class'=>'seiweb\image\storage\Storage']);
        //$this->imageManager = Yii::createObject($this->components['imageManager']);
        $this->imageManager = new ImageManager(new Driver());
    }
}

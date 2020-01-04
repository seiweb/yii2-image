<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 22.02.2016
 * Time: 1:15
 */

namespace seiweb\image\widgets;

use seiweb\image\Module;
use seiweb\image\ModuleTrait;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\widgets\InputWidget;

class AdminImages extends InputWidget
{
    public $replace = false;
    public $multiple = true;
   // public $model_attribute = null;

    public function init()
    {

        parent::init();
        if ($this->model === null) {
            throw new Exception("Need model");
        }

    }

    public function run()
    {
        $imagesProvider = new ActiveDataProvider([
           'query'=>$this->model->getImages($this->attribute),
            'pagination'=>['pageSize'=>100]
        ]);

        return $this->render('images', [
            'model' => $this->model,
            'imagesProvider'=>$imagesProvider,
            'maxFileSize'=>Module::getInstance()->uploadFileMaxSize,
            'allowedFileExtensions'=>Module::getInstance()->allowedFileExtensions,
            'sizeLimit' => Module::getInstance()->uploadFileMaxSize,
            'maxFileCount'=>Module::getInstance()->maxFileCount,
            'model_attribute'=>$this->attribute
        ]);
    }
}
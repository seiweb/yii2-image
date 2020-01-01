<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 22.02.2016
 * Time: 1:15
 */

namespace seiweb\image\widgets;

use seiweb\image\behaviors\ImageBehavior;
use seiweb\image\models\Image;
use seiweb\image\ModuleTrait;
use yii\base\Exception;
use yii\base\Widget;
use yii\data\ActiveDataProvider;

/**Виджет выведет разметку для изображения в общем списке. Нужен для обновления картинки ajax
 * Class Image
 * @package seiweb\image\widgets
 */
class OneImage extends Widget
{
    public $id = null;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        return $this->render('_image', [
            'model' => Image::findOne($this->id),
        ]);
    }
}
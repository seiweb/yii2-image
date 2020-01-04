<?php

namespace seiweb\image\actions;

use seiweb\image\models\Image;
use yii\base\Action;
use yii\helpers\Json;

class ImageDeleteAction extends Action
{
    public function run($id = null)
    {
        Image::findOne($id)->delete();
        $res = ['success' => true, 'message' => 'Изображение удалено'];
        return Json::encode($res);
    }
}

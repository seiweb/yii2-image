<?php

namespace seiweb\image\actions;

use seiweb\image\models\Image;
use seiweb\image\ModuleTrait;
use yii\base\Action;
use yii\db\Query;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;

class ImageDeleteAction extends Action
{
    public function run($id = null)
    {
        Image::findOne($id)->delete();
        $res = ['success' => true, 'message' => 'Изображение удалено'];
        return Json::encode($res);
    }
}

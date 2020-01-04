<?php

namespace seiweb\image\actions;

use seiweb\image\models\Image;
use seiweb\image\Module;
use seiweb\image\widgets\OneImage;
use Yii;
use yii\base\Action;
use yii\helpers\Json;
use yii\validators\FileValidator;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;

class ImageUploadAction extends Action
{
    public $modelAttribute = 'model_class';
    public $groupAttribute = 'model_attribute';
    public $objectAttribute = 'model_id';
    public $fileAttribute = 'swb_image';

    public function run()
    {
        if (!$model_class = \Yii::$app->request->post($this->modelAttribute)) {
            throw new BadRequestHttpException("Don't received POST param `{$this->modelAttribute}`.");
        }
        if (!$model_id = \Yii::$app->request->post($this->objectAttribute)) {
            throw new BadRequestHttpException("Don't received POST param `{$this->objectAttribute}`.");
        }

        $model_attribute = \Yii::$app->request->post($this->groupAttribute, null);

        $init = ['model_class' => $model_class, 'model_attribute' => $model_attribute, 'model_id' => $model_id];

        $replace = \Yii::$app->request->post('replace',"false");

        if($replace=="true")
        {
            foreach (Image::find()->where(['model_class'=>$model_class,'model_attribute'=>$model_attribute,'model_id'=>$model_id])->all() as $f)
                $f->delete();
        }




        $image = new Image($init);

        $ufile = UploadedFile::getInstanceByName($this->fileAttribute);

        $v = new FileValidator();
        $v->maxSize = Module::getInstance()->uploadFileMaxSize;
        $v->extensions = Module::getInstance()->allowedFileExtensions;
        $err = "Файл {$ufile->name} загружен";
        if (!$v->validate($ufile, $err)) {
            Yii::$app->response->setStatusCode(400);
            return Json::encode(['success' => false, 'message' => $err]);
        }

        $storage = Module::getInstance()->storage;

        if ($storage->processUpload($ufile,$image)) {
            $html = OneImage::widget(['id'=>$image->id]);
            $res = ['success' => true, 'message' => $err, 'html' => $html];
            return Json::encode($res);
        }

        Yii::$app->response->setStatusCode(400);
        return Json::encode(['success' => false, 'message' => $image->errors]);
    }
}

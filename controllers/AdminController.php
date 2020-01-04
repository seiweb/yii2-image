<?php

namespace seiweb\image\controllers;

use Intervention\Image\Exception\NotFoundException;
use seiweb\image\models\Image;
use seiweb\image\Module;
use seiweb\image\widgets\OneImage;
use seiweb\sortable\actions\SortableGridAction;
use Yii;
use yii\db\Query;
use yii\helpers\Json;

/**
 * Created by PhpStorm.
 * User: developer
 * Date: 19.03.2017
 * Time: 21:44
 */
class AdminController extends \yii\web\Controller
{
    public $modelAttribute = 'model_class';
    public $groupAttribute = 'model_attribute';
    public $objectAttribute = 'model_id';
    public $fileAttribute = 'swb_image';

    public function actions()
    {
        return [
            'sort' => [
                'class' => SortableGridAction::className(),
                'modelName' => \seiweb\image\models\Image::className(),
            ],

            'upload' => [
                'class' => \seiweb\image\actions\ImageUploadAction::className(),
            ],

            /*'update' => [
                'class' => FileUpdateAction::className(),
            ],*/
            'delete' => [
                'class' => \seiweb\image\actions\ImageDeleteAction::className(),
            ]

        ];
    }


    public function actionUpdate($id)
    {
        $model = Image::findOne($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Module::getInstance()->storage->flushCache($model);

                if (Yii::$app->request->isAjax) {
                    // JSON response is expected in case of successful save
                    //Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    //return ['success' => true];
                }
                return $this->renderAjax('update', [
                    'image' => $model,
                ]);
            }
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('update', [
                'image' => $model,
            ]);
        } else {
            return $this->render('update', [
                'image' => $model,
            ]);
        }
    }

    public function actionRotate($id, $angle = -90)
    {
        $image = Image::findOne($id);
        $image->rotate($angle);

        return $this->renderAjax('update', [
            'image' => $image,
        ]);
    }

    public function actionSetAsMain($id)
    {
        $image = Image::findOne($id);
        $images = (new Query())->select('id')->from(Image::tableName())->where('id<>:i', [':i' => $id])->orderBy('sort')->column();
        $newOrder = array_merge([$id], $images);
        $image->gridSort($newOrder);

        $res = ['success' => true, 'order' => $newOrder];
        return Json::encode($res);
    }

    public function actionRenderImage($id)
    {
        return OneImage::widget(['id' => $id]);
    }

    public function actionDownload($id)
    {
        $image = Image::findOne($id);
        if(!isset($image))
            throw new NotFoundException();

        return Yii::$app->response->sendFile($image->getOriginalPath().$image->file_name);
    }
}
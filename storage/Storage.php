<?php


namespace seiweb\image\storage;


use seiweb\image\models\Image;
use seiweb\image\Module;
use seiweb\image\ModuleTrait;
use Yii;
use yii\base\BaseObject;
use yii\base\Exception;

use yii\helpers\BaseFileHelper;
use yii\web\UploadedFile;

class Storage extends BaseObject
{
    public $filesRoot = '@frontend/web/uploads/images';
    public $originalDir = '_original';
    public $cacheDir = '_modified';


    /**
     * директория, в которой хранятся изображения конкретной модели
     *
     * @param seiweb\image\models\Image $image
     */
    public function getSubDirectory(\seiweb\image\models\Image $image)
    {
        $glue = '/';
        return $glue . str_replace('\\', '_', $image->model_class) . $glue;
    }

    /**
     * Генерирует новое имя файла при загрузке
     * Все кэшированные - производные от него
     * @param \seiweb\image\models\Image $image
     * @return string
     * @throws \yii\base\Exception
     */
    public function genFileName(\seiweb\image\models\Image $image)
    {
        return $image->model_id . '-' . \Yii::$app->security->generateRandomString(8);
    }

    public function processUpload(UploadedFile $file, Image $image_ar)
    {
        if ($file == null)
            throw new Exception('$file is wrong');
        if ($file) {
            \Yii::trace('file validate ok', __METHOD__);
            $image_ar->file_name = $this->genFileName($image_ar) . '.' . $file->extension;

            if(Module::getInstance()->convertToJpg)
                $image_ar->file_name = $this->genFileName($image_ar) . '.jpg';

            $image_ar->ext = $file->extension;
            $image_ar->size = $file->size;
            $image_ar->mime = $file->type;

            $dir = \Yii::getAlias($this->filesRoot . DIRECTORY_SEPARATOR . $this->originalDir . $this->getSubDirectory($image_ar));
            BaseFileHelper::createDirectory($dir, 0775, true);
            $imageFileName = $dir . DIRECTORY_SEPARATOR . $image_ar->file_name;


            /** @var \Intervention\Image\Image $image */
            $image = Module::getInstance()->imageManager->make($file->tempName);


            $image->resize(Module::getInstance()->originalResizeTo[0], Module::getInstance()->originalResizeTo[1], function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });

            /*
            if (Module::getInstance()->originalResizeMethod != null) {
                $method = Module::getInstance()->originalResizeMethod;
                if ($method == 'smart') {
                    if ($image->width() >= $image->height())
                        $method = 'widen';
                    else
                        $method = 'heighten';
                }
                $image = $image->$method(Module::getInstance()->originalResizeTo, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            */

            $image->save($imageFileName, Module::getInstance()->originalQuality, Module::getInstance()->convertToJpg?'jpg':null);
            $image_ar->size = $image->filesize();
            return $image_ar->save();
        }
        return false;
    }

    /**
     * Вписать изображение в заданные размеры
     * @param Image $image_ar
     * @param $width
     * @param $height
     * @param $position
     * @return string
     * @throws Exception
     */
    public function getFitFile(Image $image_ar, $width, $height, $position)
    {
        $fileName = 'f_' . $width . '_' . $height . '_' . $position . '_' . $image_ar->file_name;

        $origDir = Yii::getAlias($this->filesRoot . DIRECTORY_SEPARATOR . $this->originalDir . DIRECTORY_SEPARATOR . $this->getSubDirectory($image_ar) . DIRECTORY_SEPARATOR);
        $cacheDir = Yii::getAlias($this->filesRoot . DIRECTORY_SEPARATOR . $this->cacheDir . DIRECTORY_SEPARATOR . $this->getSubDirectory($image_ar) . DIRECTORY_SEPARATOR);

        $filePath = $cacheDir . $fileName;

        if (!file_exists($filePath) && file_exists($origDir . $image_ar->file_name)) {

            BaseFileHelper::createDirectory($cacheDir);

            $image = Module::getInstance()->imageManager->make($origDir . $image_ar->file_name);

            $image
                ->fit($width, $height, null, $position)
                ->save($filePath, Module::getInstance()->cachedQuality);

            if (!file_exists($filePath)) {
                throw new \Exception('Problem with image creating.');
            }
        }
        return $fileName;
    }

    /**
     * Копися ригинала изображение, используем, если нужны ватермарки
     * @param Image $image_ar
     * @return string
     * @throws Exception
     */
    public function getFullSizeFile(Image $image_ar)
    {
        $fileName = $image_ar->file_name;

        $origDir = Yii::getAlias($this->filesRoot . DIRECTORY_SEPARATOR . $this->originalDir . DIRECTORY_SEPARATOR . $this->getSubDirectory($image_ar) . DIRECTORY_SEPARATOR);
        $cacheDir = Yii::getAlias($this->filesRoot . DIRECTORY_SEPARATOR . $this->cacheDir . DIRECTORY_SEPARATOR . $this->getSubDirectory($image_ar) . DIRECTORY_SEPARATOR);

        $filePath = $cacheDir . $fileName;

        if (!file_exists($filePath) && file_exists($origDir . $image_ar->file_name)) {

            BaseFileHelper::createDirectory($cacheDir);

            $image = Module::getInstance()->imageManager->make($origDir . $image_ar->file_name);

            //TODO watermark

            /*
            if(Module::getInstance()->watermarkFilePath!=null) {
                $watermark = Module::getInstance()->imageManager->make(\Yii::getAlias(Module::getInstance()->watermarkFilePath));
                $watermark->opacity(Module::getInstance()->watermarkOpacity);
                if($image->width()>=$image->height())
                    $watermark->heighten($image->height());
                else
                    $watermark->widen($image->width());
                $image->insert($watermark, Module::getInstance()->watermarkPosition[0]);
            }
            */

            $image->save($filePath, Module::getInstance()->cachedQuality);

            if (!file_exists($filePath)) {
                throw new \Exception('Problem with image creating.');
            }
        }
        return $fileName;
    }

    public function getResizedFile(Image $image_ar, $width, $height, $keep_aspect_ratio = false)
    {

        $keep_aspect_ratio = true;

        $fileName = 'r_' . $width . '_' . $height . '_' . $image_ar->file_name;

        $origDir = Yii::getAlias($this->filesRoot . DIRECTORY_SEPARATOR . $this->originalDir . DIRECTORY_SEPARATOR . $this->getSubDirectory($image_ar) . DIRECTORY_SEPARATOR);
        $cacheDir = Yii::getAlias($this->filesRoot . DIRECTORY_SEPARATOR . $this->cacheDir . DIRECTORY_SEPARATOR . $this->getSubDirectory($image_ar) . DIRECTORY_SEPARATOR);

        $filePath = $cacheDir . $fileName;

        if (!file_exists($filePath) && file_exists($origDir . $image_ar->file_name)) {

            BaseFileHelper::createDirectory($cacheDir);

            $image = Module::getInstance()->imageManager->make($origDir . $image_ar->file_name);

            if ($keep_aspect_ratio)
                $image->resize($width, $height, function ($c) {
                    $c->aspectRatio();
                    $c->upsize();
                });
            else
                $image->resize($width, $height);

            $image->save($filePath, Module::getInstance()->cachedQuality);

            if (!file_exists($filePath)) {
                throw new \Exception('Problem with image creating.');
            }
        }
        return $fileName;
    }

    public function delete(Image $image_ar)
    {
        $origDir = Yii::getAlias($this->filesRoot . DIRECTORY_SEPARATOR . $this->originalDir . DIRECTORY_SEPARATOR . $this->getSubDirectory($image_ar) . DIRECTORY_SEPARATOR);
        $res = unlink($origDir . $image_ar->file_name);

        $this->flushCache($image_ar);
    }

    public function flushCache(Image $image_ar)
    {
        $cacheDir = Yii::getAlias($this->filesRoot . DIRECTORY_SEPARATOR . $this->cacheDir . DIRECTORY_SEPARATOR . $this->getSubDirectory($image_ar) . DIRECTORY_SEPARATOR);

        foreach (glob($cacheDir . '/*' . $image_ar->file_name) as $file)
            unlink($file);
    }

    public function rotate(Image $image_ar, $angle)
    {
        $origDir = Yii::getAlias($this->filesRoot . DIRECTORY_SEPARATOR . $this->originalDir . DIRECTORY_SEPARATOR . $this->getSubDirectory($image_ar) . DIRECTORY_SEPARATOR);
        $image = Module::getInstance()->imageManager->make($origDir . $image_ar->file_name);
        $image->rotate($angle);
        $image->save();
        $this->flushCache($image_ar);
        // $this->updateCounters(['version'=>1]);
    }
}
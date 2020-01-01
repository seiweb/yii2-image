<?php
/**
 * @link https://github.com/timurmelnikov/yii2-loading-overlay
 * @copyright Copyright (c) 2017 Timur Melnikov
 * @license MIT
 */

namespace seiweb\image\assets;

use yii\web\AssetBundle;

/**
 * Класс подключения скрипта jQuery LoadingOverlay
 */
class LoadingOverlayAsset extends AssetBundle
{
    public $sourcePath = '@bower/gasparesganga-jquery-loading-overlay';
    public $depends = [
        'yii\web\JqueryAsset',
    ];


    /**
    * Метод инициализации
    */
    public function init()
    {
        parent::init();
        $this->js[] = YII_DEBUG ? 'src/loadingoverlay.js' : 'src/loadingoverlay.min.js';
    }
}

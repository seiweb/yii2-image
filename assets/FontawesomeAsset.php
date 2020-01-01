<?php

namespace seiweb\image\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class FontawesomeAsset extends AssetBundle
{
    public $sourcePath = '@bower/font-awesome';
    public $css = [
        'css/all.min.css',
    ];
}

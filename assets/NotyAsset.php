<?php
/**
 * NotyAsset Class File
 *
 * This is a helper class which is used to register required widget assets.
 *
 * @author Mohammad Shifreen
 * @link http://www.yiiframework.com/extension/yii2-noty/
 * @copyright 2016 Mohammed Shifreen
 * @license https://github.com/Shifrin/yii2-noty/blob/master/LICENSE.md
 */

namespace seiweb\image\assets;

use yii\web\AssetBundle;


class NotyAsset extends AssetBundle
{

    public $sourcePath = '@bower/noty';
    public $js = [
        'lib/noty.js'
    ];
    public $css = [
        'lib/noty.css'
    ];
}
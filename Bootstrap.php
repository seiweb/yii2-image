<?php


namespace seiweb\image;


use yii\base\Application;
use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{

    /**
     * @inheritDoc
     */
    public function bootstrap($app)
    {
        \Yii::$app->getModule('swb_image');
    }
}
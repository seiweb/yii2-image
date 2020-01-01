<?php
namespace seiweb\image;

use yii\base\Exception;

trait ModuleTrait
{
    /**
     * @var \seiweb\image\Module
     */
    private $_module;

    /**
     * @return null|Module|\yii\base\Module
     * @throws Exception
     */
    protected function getModule()
    {
        if ($this->_module == null) {
            $this->_module = \Yii::$app->getModule('swb_image');
        }

        if(!$this->_module){
            throw new Exception("\n\n\n\n\nYii2 image module not found, may be you didn't add it to your config?\n\n\n\n");
        }

        return $this->_module;
    }
}
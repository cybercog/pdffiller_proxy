<?php

namespace maxlen\proxy;

use yii\console\Exception;

class ProxyCron extends \yii\base\Module
{
    /**
     * @var string
     */
    public $controllerNamespace = 'maxlen\proxy\controllers';

    public function init()
    {
        parent::init();
    }
}

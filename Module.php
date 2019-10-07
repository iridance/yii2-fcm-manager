<?php

namespace fcm\manager;

/**
 * fcm module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'fcm\manager\controllers';

    public $defaultRoute = 'notification';

    public $layout = '@fcm/manager/views/layouts/main.php';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        // custom initialization code goes here
    }
}

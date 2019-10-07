<?php

namespace fcm\manager\assets;

use yii\web\AssetBundle;

class FontAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://fonts.googleapis.com/css?family=Noto+Sans+TC',
        //'/css/font.css',
    ];

    public $js = [

    ];

    public $depends = [

    ];
}

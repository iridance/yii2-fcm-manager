<?php

namespace fcm\manager\assets;

use yii\web\AssetBundle;

class LoadingAsset extends AssetBundle
{
    public $sourcePath = '@npm/gasparesganga-jquery-loading-overlay/dist';

    public $js = [
        'loadingoverlay.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
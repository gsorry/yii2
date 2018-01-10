<?php

namespace pine\yii\widgets;

use yii\web\AssetBundle;

/**
 * Class FileInputAsset
 * @package pine\yii\widgets
 */
class FileInputAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $basePath = '@webroot';

    /**
     * @var string
     */
    public $baseUrl = '@web';

    /**
     * @var array
     */
    public $css = [];

    /**
     * @var array
     */
    public $js = [];

    /**
     * @var array
     */
    public $depends = [
        'app\assets\AppAsset',
    ];
}

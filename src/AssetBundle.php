<?php

namespace sandritsch91\yii2\autosize;

/**
 * Class AssetBundle
 * @package sandritsch91\yii2\autosize
 */
class AssetBundle extends \yii\web\AssetBundle
{
    public $sourcePath = '@bower/autosize/dist';
    public $js = [
        'autosize.js',
    ];
}

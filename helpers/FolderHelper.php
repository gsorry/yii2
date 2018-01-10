<?php

namespace pine\yii\helpers;

use yii\helpers\BaseFileHelper;
use yii\web\UploadedFile;
use yii\db\ActiveRecord;

/**
 * Class FolderHelper
 * @package pine\yii\helpers
 */
class FolderHelper
{
    /**
     * Get Date Path returns new folder path based on year, month and day.
     * Example: 2015\05\23\
     * Settings options: parentPath - where to save generated path
     *                   childPath - add this folder into generated path
     *                   rootFolder - default is 'uploads' folder
     *
     * @param array Settings
     * @return string New folder path
     */
    public static function getDatePath($settings = [])
    {
        $rootFolder = isset($settings['rootFolder']) && $settings['rootFolder'] != '' ? $settings['rootFolder'] : 'uploads';

        $path = $rootFolder . DIRECTORY_SEPARATOR . date('Y') . DIRECTORY_SEPARATOR . date('m') . DIRECTORY_SEPARATOR . date('d') . DIRECTORY_SEPARATOR;

        if (isset($settings['parentPath']) && $settings['parentPath'] != '') {
            $parentPath = trim($settings['parentPath'], DIRECTORY_SEPARATOR);
            $path = $parentPath . DIRECTORY_SEPARATOR . $path;
        }

        if (isset($settings['childPath']) && $settings['childPath'] != '') {
            $childPath = trim($settings['childPath'], DIRECTORY_SEPARATOR);
            $path = $path . $childPath . DIRECTORY_SEPARATOR;
        }

        BaseFileHelper::createDirectory($path, 0755);

        return $path;
    }

    /**
     * Get Model Path returns new folder path based on model, primary key and property.
     * Example: app_model_User\23\photo\ (NOTE: you can notice namespace where slash replaced with underscore to avoid issues on windows operating systems)
     * Settings options: parentPath - where to save generated path
     *                   childPath - add this folder into generated path
     *                   rootFolder - default is 'uploads' folder
     *
     * @param UploadedFile File
     * @param ActiveRecord Model
     * @param string Property
     * @param array Settings
     * @return string New folder path
     */
    public static function getModelPath($model, $property, $settings = [])
    {
        $rootFolder = isset($settings['rootFolder']) && $settings['rootFolder'] != '' ? $settings['rootFolder'] : 'uploads';

        $path = $rootFolder . DIRECTORY_SEPARATOR . str_replace('\\', '_', get_class($model)) . DIRECTORY_SEPARATOR . $model->primaryKey . DIRECTORY_SEPARATOR . $property. DIRECTORY_SEPARATOR;

        if (isset($settings['parentPath']) && $settings['parentPath'] != '') {
            $parentPath = trim($settings['parentPath'], DIRECTORY_SEPARATOR);
            $path = $parentPath . DIRECTORY_SEPARATOR . $path;
        }

        if (isset($settings['childPath']) && $settings['childPath'] != '') {
            $childPath = trim($settings['childPath'], DIRECTORY_SEPARATOR);
            $path = $path . $childPath . DIRECTORY_SEPARATOR;
        }

        BaseFileHelper::createDirectory($path, 0755);

        return $path;
    }
}

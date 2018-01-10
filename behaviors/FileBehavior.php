<?php

namespace pine\yii\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\Event;
use yii\db\BaseActiveRecord;
use yii\web\UploadedFile;
use yii\helpers\BaseFileHelper;
use pine\yii\helpers\FolderHelper;
use app\modules\file\models\File;

/**
 * Class FileBehavior
 * @package pine\yii\behaviors
 */
class FileBehavior extends \yii\base\Behavior
{
    /**
     * Events
     *
     * @return array
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_INSERT => 'eventAfterSave',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'eventAfterSave',
        ];
    }

    /**
     * Event After Save
     */
    public function eventAfterSave()
    {
        $rules = $this->owner->rules();

        if (count($rules) > 0)
        foreach ($rules as $rule) {
            if ('file' == $rule[1]) {
                $attributes = $rule[0];
                $options = array_slice($rule, 2);
                if (count($attributes) > 0) {
                    foreach ($attributes as $attribute) {
                        if (Yii::$app->request->isPost) {
                            if (isset($options['maxFiles']) && $options['maxFiles'] > 1) {
                                $this->owner->{$attribute} = UploadedFile::getInstances($this->owner, $attribute);
                                if ($this->owner->{$attribute} && $this->owner->validate()) {
                                    foreach ($this->owner->{$attribute} as $file) {
                                        $filePath = FolderHelper::getModelPath($this->owner, $attribute) . date('Y-m-d H:i:s ') . $file->baseName . '.' . $file->extension;
                                        if ($file->saveAs($filePath)) {
                                            $fileModel = new File();
                                            $fileModel->path = $filePath;
                                            $fileModel->name = $file->name;
                                            $fileModel->extension = $file->extension;
                                            $fileModel->kind = $file->type;
                                            $fileModel->size = $file->size;
                                            $fileModel->model = get_class($this->owner);
                                            $fileModel->property = $attribute;
                                            $fileModel->pk = $this->owner->primaryKey;
                                            $fileModel->save();
                                        }
                                    }
                                }
                            } else {
                                $this->owner->{$attribute} = UploadedFile::getInstance($this->owner, $attribute);
                                if ($this->owner->{$attribute} && $this->owner->validate()) {
                                    $filePath = FolderHelper::getModelPath($this->owner, $attribute) . date('Y-m-d H:i:s ') . $this->owner->{$attribute}->baseName . '.' . $this->owner->{$attribute}->extension;
                                    if ($this->owner->{$attribute}->saveAs($filePath)) {
                                        $fileModel = File::find()->where([
                                            'deleted' => 0,
                                            'model' => get_class($this->owner),
                                            'property' => $attribute,
                                            'pk' => $this->owner->primaryKey,
                                        ])->one();
                                        if ($fileModel !== NULL) {
                                            BaseFileHelper::removeDirectory($fileModel->path);
                                            $fileModel->path = $filePath;
                                            $fileModel->name = $this->owner->{$attribute}->name;
                                            $fileModel->extension = $this->owner->{$attribute}->extension;
                                            $fileModel->kind = $this->owner->{$attribute}->type;
                                            $fileModel->size = $this->owner->{$attribute}->size;
                                        } else {
                                            $fileModel = new File();
                                            $fileModel->path = $filePath;
                                            $fileModel->name = $this->owner->{$attribute}->name;
                                            $fileModel->extension = $this->owner->{$attribute}->extension;
                                            $fileModel->kind = $this->owner->{$attribute}->type;
                                            $fileModel->size = $this->owner->{$attribute}->size;
                                            $fileModel->model = get_class($this->owner);
                                            $fileModel->property = $attribute;
                                            $fileModel->pk = $this->owner->primaryKey;
                                        }
                                        $fileModel->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

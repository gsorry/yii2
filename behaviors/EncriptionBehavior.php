<?php

namespace pine\yii\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\Event;
use yii\db\BaseActiveRecord;
use pine\yii\helpers\CryptHelper;

class EncriptionBehavior extends \yii\base\Behavior
{
    public $attributes = [];

    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_FIND => 'eventAfterFind',
            BaseActiveRecord::EVENT_BEFORE_VALIDATE => 'eventBeforeValidate',
        ];
    }

    public function eventAfterFind()
    {
        if (count($this->attributes) > 0) {
            foreach ($this->attributes as $attribute) {
                $this->owner->{$attribute} = CryptHelper::decrypt($this->owner->{$attribute});
            }
        }
    }

    public function eventBeforeValidate()
    {
        if (count($this->attributes) > 0) {
            foreach ($this->attributes as $attribute) {
                $this->owner->{$attribute} = CryptHelper::encrypt($this->owner->{$attribute});
            }
        }
    }
}
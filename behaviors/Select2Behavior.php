<?php

namespace pine\yii\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\Event;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;

class Select2Behavior extends \yii\base\Behavior
{
    public $attributes = [];
    public $from = 'id';
    public $to = 'name';

    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_FIND => 'eventAfterFind',
        ];
    }

    public function eventAfterFind()
    {
        if (count($this->attributes) > 0) {
            foreach ($this->attributes as $attribute => $relation) {
                $relationMethod = 'get'.ucfirst($relation);
                $this->owner->{$attribute} = implode(',', ArrayHelper::map($this->owner->{$relation}, $this->from, $this->to));
            }
        }
    }

}
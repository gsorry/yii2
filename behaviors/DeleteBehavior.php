<?php

namespace pine\yii\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\Event;
use yii\db\BaseActiveRecord;

class DeleteBehavior extends \yii\base\Behavior
{
    public $deletedAttribute = 'deleted';

    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'eventBeforeInsert',
            BaseActiveRecord::EVENT_BEFORE_DELETE => 'eventBeforeDelete',
        ];
    }

    public function eventBeforeInsert()
    {
        $this->owner->deleted = 0;
    }

    public function eventBeforeDelete($event)
    {
        $model = $this->owner;
        $user = Yii::$app->get('user', false);
        $model->{$this->deletedAttribute} = 1;
        $model->save();
        $event->isValid = false;
    }
}

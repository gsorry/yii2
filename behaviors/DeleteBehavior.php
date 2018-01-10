<?php

namespace pine\yii\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\Event;
use yii\db\BaseActiveRecord;

/**
 * Class DeleteBehavior
 * @package pine\yii\behaviors
 */
class DeleteBehavior extends \yii\base\Behavior
{
    /**
     * @var string
     */
    public $deletedAttribute = 'deleted';

    /**
     * Events
     *
     * @return array
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'eventBeforeInsert',
            BaseActiveRecord::EVENT_BEFORE_DELETE => 'eventBeforeDelete',
        ];
    }

    /**
     * Event Before Insert
     */
    public function eventBeforeInsert()
    {
        $this->owner->deleted = 0;
    }

    /**
     * Event Before Delete
     *
     * @param $event
     */
    public function eventBeforeDelete($event)
    {
        $model = $this->owner;
        $user = Yii::$app->get('user', false);
        $model->{$this->deletedAttribute} = 1;
        $model->save();
        $event->isValid = false;
    }
}

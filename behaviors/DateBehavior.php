<?php

namespace pine\yii\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\Event;
use yii\db\BaseActiveRecord;

/**
 * Class DateBehavior
 * @package pine\yii\behaviors
 */
class DateBehavior extends \yii\base\Behavior
{
    /**
     * @var array
     */
    public $attributes = [];

    /**
     * Events
     *
     * @return array
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_FIND => 'eventAfterFind',
            BaseActiveRecord::EVENT_BEFORE_VALIDATE => 'eventBeforeValidate',
        ];
    }

    /**
     * Event After Find
     */
    public function eventAfterFind()
    {
        if (count($this->attributes) > 0) {
            foreach ($this->attributes as $attribute) {
                $this->owner->{$attribute} = \Yii::$app->formatter->asDate($this->owner->{$attribute});
            }
        }
    }

    /**
     * Event Before Validate
     */
    public function eventBeforeValidate()
    {
        if (count($this->attributes) > 0) {
            foreach ($this->attributes as $attribute) {
                $this->owner->{$attribute} = \Yii::$app->formatter->asDateTime($this->owner->{$attribute}, 'yyyy-MM-dd 00:00:00');
            }
        }
    }
}
